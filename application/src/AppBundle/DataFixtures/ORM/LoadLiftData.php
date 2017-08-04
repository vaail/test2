<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Lift;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLiftData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager)
    {
        $building = $this->getReference('building:1');

        for($i = 1; $i <= 4; $i++) {
            $lift = new Lift();
            $lift
                ->setBuilding($building)
                ->setCode("bld1_lft${i}")
                ->setCurrentFloor(1);

            $manager->persist($lift);
            $manager->flush();

            $this->addReference("lift:${i}", $lift);
        }
    }

    public function getOrder()
    {
        return 2;
    }
}