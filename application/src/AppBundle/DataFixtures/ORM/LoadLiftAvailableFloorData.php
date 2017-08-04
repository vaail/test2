<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\LiftAvailableFloor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLiftAvailableFloorData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 4; $i++) {
            $lift = $this->getReference("lift:${i}");
            for($j = 1; $j <= ($i <= 2 ? 25 : 15); $j++) {
                $availableFloors = new LiftAvailableFloor();
                $availableFloors
                    ->setLift($lift)
                    ->setFloor($j);

                $manager->persist($availableFloors);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}