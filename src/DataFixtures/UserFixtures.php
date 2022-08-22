<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private Faker\Generator $faker;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Faker\Factory::create();
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 40; $i++) {
            $user = new User();
            $user->setFirstName($this->faker->firstName);
            $user->setLastName($this->faker->lastName);
            $user->setEmail($this->faker->email);
            $user->setType($this->faker->numberBetween(1, 3));
            //some users might be without nickname
            if (rand(0, 1)) {
                $user->setNickname($this->faker->unique()->word . $this->faker->optional(0.5)->numberBetween(1, 9000));
            }
            $user->setIsVerified(true);
            $user->setRoles(['ROLE_USER']);
            $user->setAbout($this->faker->realText($this->faker->numberBetween(50, 100)));
            $password = $this->hasher->hashPassword($user, '123123123');
            $user->setPassword($password);
            $user->setPicture('default_img.jpeg');
            $user->setCreatedAt(new \DateTimeImmutable('now'));
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
