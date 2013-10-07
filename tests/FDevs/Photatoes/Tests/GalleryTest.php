<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/30/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;

class GalleryTest extends TestCase
{
    public function testId()
    {
        $album = $this->getGallery('newTestId');
        $this->assertEquals('newTestId', $album->getId());
    }

    public function testReload()
    {
        $album = $this->getGallery();
        $this->assertCount(0, $album->getImages());

        $album->setManager($this->getManger());
        $album->reload();
        $this->assertEquals('test album', $album->getName());
        $this->assertCount(1, $album->getImages());
        $this->assertCount(1, $album->getCover());

        $album->reload(true);
        $this->assertCount(1, $album->getCover());
        $this->assertCount(2, $album->getImages());
    }

    public function testImages()
    {
        $album = $this->getGallery();
        $this->assertCount(0, $album->getImages());

        $album->setManager($this->getManger());
        $this->assertCount(1, $album->getImages());

        $album->addImage($this->getImage());
        $this->assertCount(2, $album->getImages());

        $album->setImages(array($this->getImage()));
        $this->assertCount(1, $album->getImages());
    }

    public function testName()
    {
        $album = $this->getGallery();
        $this->assertNull($album->getName());

        $album->setName('name album');
        $this->assertEquals('name album', $album->getName());
    }

    public function testCount()
    {
        $album = $this->getGallery();

        $this->assertEquals(0, $album->getCount());

        $album->setCount(100);
        $this->assertEquals(100, $album->getCount());

        $album->setImages(array());
        $this->assertEquals(0, $album->getCount());

        $album->addImage($this->getImage());
        $this->assertEquals(1, $album->getCount());
    }

    public function testCover()
    {
        $album = $this->getGallery();
        $this->assertCount(0, $album->getCover());

        $album->addCover($this->getMeta('', 50, 50));
        $this->assertCount(1, $album->getCover());
    }

    public function testUpdatedAt()
    {
        $album = $this->getGallery();
        $this->assertNull($album->getUpdatedAt());

        $album->setUpdatedAt(new \DateTime());
        $this->assertInstanceOf('DateTime', $album->getUpdatedAt());
    }

    public function testSerializable()
    {
        $album = $this->getGallery();
        $album->addCover($this->getMeta('cover url', 150, 150));
        $album->addImage($this->getImage());
        $album->setName('album Name');
        $album->setUpdatedAt(new \DateTime());

        /** @var \FDevs\Photatoes\Gallery $s */
        $s = unserialize(serialize($album));
        $this->assertEquals('album Name', $s->getName());

        $album->setManager($this->getManger());
        $s = unserialize(serialize($album));
        $this->assertEquals('test album', $s->getName());
        $this->assertCount(2, $s->getCover());
        $this->assertCount(2, $s->getImages());
    }
}
