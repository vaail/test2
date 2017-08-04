<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Building;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBuildingData extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager)
    {
        $building = new Building();
        $building->setName('Building #1');
        $building->setFloorsCount(25);

        $manager->persist($building);
        $manager->flush();

        $this->addReference('building:1', $building);
    }

    public function getOrder()
    {
        return 1;
    }
}