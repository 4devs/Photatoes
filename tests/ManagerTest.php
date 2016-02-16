<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 10/1/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;

class ManagerTest extends TestCase
{
    public function testAdapter()
    {
        $manager = $this->getManger();
        $this->assertInstanceOf('FDevs\Photatoes\Adapter\AdapterInterface', $manager->getAdapter());
    }

    public function testImage()
    {
        $manager = $this->getManger();
        $this->assertInstanceOf('FDevs\Photatoes\Image', $manager->getImage('key'));
    }

    public function testGallery()
    {
        $manager = $this->getManger();
        $this->assertInstanceOf('FDevs\Photatoes\Gallery', $manager->getGallery('key'));
    }

    public function testGalleyList()
    {
        $manager = $this->getManger();
        $this->assertCount(1, $manager->getGalleryList());
        $this->assertInternalType('array', $manager->getGalleryList());
    }

    public function testGetTagList()
    {
        $manager = $this->getManger();
        $this->assertCount(1, $manager->getTagList());
    }

    public function testGetImagesByTag()
    {
        $manager = $this->getManger();
        $this->assertCount(1, $manager->getImagesByTag('testTag'));
        $this->assertInstanceOf('FDevs\Photatoes\Image', current($manager->getImagesByTag('testTag')));
    }
}
