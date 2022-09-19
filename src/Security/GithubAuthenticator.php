<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GithubAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{

    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'github_auth';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('github');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GithubResourceOwner $githubUser */
                $githubUser = $client->fetchUserFromToken($accessToken);
                // 1) check have they logged in with Github before
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['githubId' => $githubUser->getId()]);
                if ($existingUser) {
                    return $existingUser;
                }
                //If user hide email
                $email = $githubUser->getEmail();
                if (!$email) {
                    throw new AuthenticationException(
                        "For authentification via github make public visible your email in github profile"
                    );
                }

                // 2) do we have a matching user by email?
                //write githubId fo future authentications
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($user) {
                    $user->setGithubId($githubUser->getId());
                    $user->setUpdatedAt(new \DateTimeImmutable('now'));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    return $user;
                }

                // 3) if dont find existing user, "register" them by creating
                // a User object
                $newUser = new User();
                [$firstName, $lastName] = explode(" ", $githubUser->getName());
                $newUser->setFirstName($firstName);
                $newUser->setLastName(!empty($lastName) ? $lastName : $githubUser->getNickname());
                $newUser->setEmail($email);
                $newUser->setType(1);
                $newUser->setRoles(['ROLE_USER']);
                $plainPassword = random_int(11111111, 99999999);
                $password = $this->hasher->hashPassword($newUser, $plainPassword);
                $newUser->setPassword($password);
                $newUser->setPicture('default_avatar.png');
                $newUser->setCreatedAt(new \DateTimeImmutable('now'));
                $newUser->setUpdatedAt(new \DateTimeImmutable('now'));
                $newUser->setGithubId($githubUser->getId());
                $this->entityManager->persist($newUser);
                $this->entityManager->flush();

                return $newUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('my_profile');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = $exception->getMessage();

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/login/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}