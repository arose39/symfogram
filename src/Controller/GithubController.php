<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    //Link to this controller to start the "connect" process
    #[Route('/connect/github', name: 'connect_github_start')]
    public function redirectToGithubConnect(ClientRegistry $clientRegistry)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('profile', ['nickname' => $this->getUser()->getNickname()]);
        }

        return $clientRegistry
            ->getClient('github') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'user:email',
                'read:user'// the scopes you want to access
            ]);
    }

    // After going to Google, you're redirected back here
    // because this is the "redirect_route" you configured
    // in config/packages/knpu_oauth2_client.yaml
    #[Route('/github/auth', name: 'github_auth')]
    public function connectGithubCheck()
    {
    }
}
