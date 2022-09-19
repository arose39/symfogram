<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
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

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
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
        return $request->attributes->get('_route') === 'google_auth';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();

                // 1) check they logged in with Google before
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);
                if ($existingUser) {
                    return $existingUser;
                }

                // 2) do we have a matching user by email?
                //write googleId fo future authentications
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($user) {
                    $user->setGoogleId($googleUser->getId());
                    $user->setUpdatedAt(new \DateTimeImmutable('now'));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    return $user;
                }

                // 3) if dont find existing user, "register" them by creating
                // a User object
                $newUser = new User();
                $newUser->setFirstName($googleUser->getFirstName());
                $newUser->setLastName($googleUser->getLastName());
                $newUser->setEmail($email);
                $newUser->setType(1);
                $newUser->setRoles(['ROLE_USER']);
                $plainPassword = random_int(11111111, 99999999);
                $password = $this->hasher->hashPassword($newUser, $plainPassword);
                $newUser->setPassword($password);
                $newUser->setPicture('default_avatar.png');
                $newUser->setCreatedAt(new \DateTimeImmutable('now'));
                $newUser->setUpdatedAt(new \DateTimeImmutable('now'));
                $newUser->setGoogleId($googleUser->getId());
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
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

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