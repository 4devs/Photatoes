<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 10/2/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;

class ImageTest extends TestCase
{
    public function testGetId()
    {
        $this->assertEquals('testId', $this->getImage()->getId());
    }

    public function testSave()
    {
        $image = $this->getImage();
        $this->assertFalse($image->save());

        $image->setManager($this->getManger());
        $this->assertTrue($image->save());
    }

    public function testDescription()
    {
        $image = $this->getImage();
        $this->assertNull($image->getDescription());

        $image->setDescription('description');
        $this->assertEquals('description', $image->getDescription());

    }

    public function testImg()
    {
        $image = $this->getImage();
        $this->assertCount(0, $image->all());
        $this->assertNull($image->get('xxl'));

        $image->addMeta($this->getMeta('', 50, 50));
        $this->assertCount(1, $image->all());
        $this->assertInstanceOf('FDevs\Photatoes\Meta', $image->get('xxxs'));
    }

    public function testPublishAt()
    {
        $image = $this->getImage();
        $this->assertNull($image->getPublishAt());

        $image->setPublishAt(new \DateTime());
        $this->assertInstanceOf('DateTime', $image->getPublishAt());
    }

    public function testUpdateAt()
    {
        $image = $this->getImage();
        $this->assertNull($image->getUpdateAt());

        $image->setUpdateAt(new \DateTime());
        $this->assertInstanceOf('DateTime', $image->getUpdateAt());
    }

    public function testTitle()
    {
        $image = $this->getImage();
        $this->assertNull($image->getTitle());

        $image->setTitle('title');
        $this->assertEquals('title', $image->getTitle());
    }

    public function testTags()
    {
        $image = $this->getImage();
        $this->assertCount(0, $image->getTags());

        $image->addTag('tag');
        $this->assertCount(1, $image->getTags());

        $image->setTags(array('tag1', 'tag2'));
        $this->assertCount(2, $image->getTags());
    }

    public function testHas()
    {
        $image = $this->getImage();
        $this->assertFalse($image->has());

        $image->setPublishAt(new \DateTime());
        $this->assertTrue($image->has());
    }

    public function testSerializable()
    {
        $image = $this->getImage();
        $image->setTitle('title');
        $image->setDescription('description');
        $image->setPublishAt(new \DateTime());
        $image->setUpdateAt(new \DateTime());
        $image->addMeta($this->getMeta());
        $image->addMeta($this->getMeta());
        $image->setManager($this->getManger());

        /** @var \FDevs\Photatoes\Image $s */
        $s = unserialize(serialize($image));
        $this->assertEquals('title', $s->getTitle());
        $this->assertInstanceOf('DateTime', $s->getUpdateAt());
        $this->assertInstanceOf('DateTime', $s->getPublishAt());
        $this->assertCount(1, $s->all());
    }

}
