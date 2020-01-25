<?php

namespace App\DataFixtures;

use App\Entity\Hydrometer;
use App\Entity\Token;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User;
        $user
            ->setEmail('test@example.com');

        $token = new Token();
        $token
            ->setType('device')
            ->setValue(bin2hex(random_bytes(10)))
            ->setUser($user);

        $hydrometer = new Hydrometer();
        $hydrometer->setUser($user);
        $hydrometer->setToken($token);

        $manager->persist($user);
        $manager->persist($token);
        $manager->persist($hydrometer);

        $this->setReference('test-user', $user);
        $this->setReference('test-token', $token);
        $this->setReference('test-hydrometer', $hydrometer);

        $manager->flush();
    }
}
