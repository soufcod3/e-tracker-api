<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BudgetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 0; $i < 3; $i++) {
            $budget = new Budget();
            $budget->setName('budget ' . $i+1);
            $budget->setKickoffAmount(rand(100, 700));
            $budget->setLeftAmount(rand(0,100));

            $manager->persist($budget);
        }
        $manager->flush();
    }
}
