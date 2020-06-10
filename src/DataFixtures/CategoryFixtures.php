<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cat1 = new Category();
        $cat1->setName("Game (Unity)");
        $manager->persist($cat1);

        $cat2 = new Category();
        $cat2->setName("Website");
        $manager->persist($cat2);

        $cat3 = new Category();
        $cat3->setName("OTHER");
        $manager->persist($cat3);

        $manager->flush();
    }
}
