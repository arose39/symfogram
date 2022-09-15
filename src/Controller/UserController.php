<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Fileters\UsersFilter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users_page')]
    public function index(ManagerRegistry $doctrine, Request $request, UserRepository $userRepository): Response
    {
        $usersFilter = new UsersFilter($userRepository, $request);
        $users = $usersFilter->getFilteredBuilder()->orderBy('user.first_name', "ASC")->getQuery()->getResult();

        return $this->render('users/index.html.twig', [
            'users' => $users,
            'request' => $request
        ]);
    }
}
