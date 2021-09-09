<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
     private $passwordEncoder;
     private $manager;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $roleAdmin = new Role();
        $roleAdmin->setName("ROLE_ADMIN");
        $this->manager->persist($roleAdmin);

        $roleUser = new Role();
        $roleUser->setName("ROLE_USER");
        $this->manager->persist($roleUser);


        // create objects
        $this->createUser('admin@admin.com', 'myblackcat123', $roleAdmin);
        $this->createUser('Lauren.M.Maher@mytudublin.ie', 'housegame99', $roleUser);
        $this->createUser('nina.lyons@tudublin.ie', 'Mypassword', $roleUser);
    }

    private function createUser($username, $plainPassword, $role)
    {
        $user = new User();
        $user->setEmail($username);
        $user->setRole($role);

        // password - and encoding
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->manager->persist($user);

        // send query to DB
        $this->manager->flush();
    }
}