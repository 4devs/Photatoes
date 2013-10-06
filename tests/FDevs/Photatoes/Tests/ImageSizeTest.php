<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 10/1/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;


use FDevs\Photatoes\ImageSize;

class ImageSizeTest extends TestCase
{
    public function testGet()
    {
        $this->assertNull(ImageSize::get('size not found'));
        $this->assertEquals('XXL', ImageSize::get('XXL'));
        $this->assertEquals('XXL', ImageSize::get('xxL'));
    }

    public function testHas()
    {
        $this->assertTrue(ImageSize::has('XL'));
        $this->assertFalse(ImageSize::has('size not found'));
    }

    public function testGetSize()
    {
        $this->assertEquals('XXL', ImageSize::getSize(1024, 765));
        $this->assertEquals('XXL', ImageSize::getSize(1023, 0));
        $this->assertEquals('XL', ImageSize::getSize(800, 0));
        $this->assertEquals('L', ImageSize::getSize(500, 499));
        $this->assertEquals('M', ImageSize::getSize(299, 300));
        $this->assertEquals('orig', ImageSize::getSize(0, 0));
        $this->assertEquals('orig', ImageSize::getSize(1025, 0));
        $this->assertEquals('orig', ImageSize::getSize(1025, 0));
    }
} 