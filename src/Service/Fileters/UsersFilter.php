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

    public function firstName(?string $searchString = null): QueryBuilder
    {
        if ($searchString != null) {
            $searchString = ucfirst(strtolower($searchString));
            return $this->builder
                ->andWhere("user.first_name LIKE :firstName")
                ->setParameter("firstName", '%' . $searchString . '%');
        }

        return $this->builder;
    }

    public function lastName(?string $searchString = null): QueryBuilder
    {
        if ($searchString != null) {
            $searchString = ucfirst(strtolower($searchString));
            return $this->builder
                ->andWhere("user.last_name LIKE :lastName")
                ->setParameter("lastName", '%' . $searchString . '%');
        }

        return $this->builder;
    }

    public function nickname(?string $searchString = null): QueryBuilder
    {
        if ($searchString != null) {
            return $this->builder
                ->andWhere("user.nickname LIKE :nickname")
                ->setParameter("nickname", '%' . $searchString . '%');
        }

        return $this->builder;
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
