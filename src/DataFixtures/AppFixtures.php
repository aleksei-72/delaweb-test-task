<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Organization;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $organization = new Organization();
        $organization->setTitle('DelaWeb');

        $manager->persist($organization);


        $user = new User();
        $user->setFirstName('Антон');
        $user->setLastName('Кузнецов');
        $user->setPhone('88005553555');
        $user->setPassword('P@ssw0rd');
        $user->setOrganization($organization);
        $user->setInvitatory($user);

        $manager->persist($user);


        $manager->flush();


    }
}
