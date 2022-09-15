<?php declare(strict_types=1);

namespace App\Service\Fileters;

use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class UsersFilter
{
    private QueryBuilder $builder;

    public function __construct(private UserRepository $userRepository, private Request $request)
    {
        $this->builder = $this->userRepository->createQueryBuilder('user');
    }

    public function firstName(string $searchString = ''): QueryBuilder
    {
        return $this->builder
            ->andWhere("user.first_name LIKE :firstName")
            ->setParameter("firstName", '%' . $searchString . '%');
    }

    public function lastName(string $searchString = ''): QueryBuilder
    {
        return $this->builder
            ->andWhere("user.last_name LIKE :lastName")
            ->setParameter("lastName", '%' . $searchString . '%');
    }

    public function getFilteredBuilder(): QueryBuilder
    {
        foreach ($this->request->query->all() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func([$this, $name], $value);
            }
        }

        return $this->builder;
    }
}