<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/30/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;

class MetaTest extends TestCase
{
    public function testHeight()
    {
        $meta = $this->getMeta();
        $this->assertEquals(0, $meta->getHeight());
        $meta->setHeight(100);
        $this->assertEquals(100, $meta->getHeight());
    }

    public function testHref()
    {
        $meta = $this->getMeta();
        $this->assertEquals('', $meta->getHref());
        $meta->setHref('test_href');
        $this->assertEquals('test_href', $meta->getHref());
    }

    public function testSize()
    {
        $meta = $this->getMeta();
        $this->assertEquals('orig', $meta->getSize());

        $meta->setSize('XXl');
        $this->assertEquals('XXL', $meta->getSize());

        $meta = $this->getMeta('http://', 50, 50);
        $this->assertEquals('XXXS', $meta->getSize());

    }

    public function testWidth()
    {
        $meta = $this->getMeta();
        $this->assertEquals(0, $meta->getWidth());
        $meta->setWidth(100);
        $this->assertEquals(100, $meta->getWidth());
    }

    public function testSerializable()
    {
        $meta = $this->getMeta('http', 100, 200);
        $this->assertInstanceOf('Serializable', $meta);

        /** @var \FDevs\Photatoes\Meta $s */
        $s = unserialize(serialize($meta));
        $this->assertInstanceOf('FDevs\Photatoes\Meta', $s);
        $this->assertEquals('http',$s->getHref());
        $this->assertEquals(100,$s->getWidth());
        $this->assertEquals(200,$s->getHeight());
        $this->assertEquals('M',$s->getSize());
    }
}
