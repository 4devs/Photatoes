<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/30/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests;

use FDevs\Photatoes\Adapter\AdapterInterface;
use FDevs\Photatoes\Gallery;
use FDevs\Photatoes\Image;
use FDevs\Photatoes\Manager;
use FDevs\Photatoes\Meta;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Manager
     * @param  AdapterInterface $adapter
     */
    protected function getManger(AdapterInterface $adapter = null)
    {
        if (!$adapter) {
            $adapter = $this->getMockAdapter();
        }

        return new Manager($adapter);
    }

    /**
     * @param  string $href
     * @param  int    $width
     * @param  int    $height
     * @return Meta
     */
    protected function getMeta($href = '', $width = 0, $height = 0)
    {
        return new Meta($href, $width, $height);
    }

    /**
     *
     * @param  string $data
     * @return Image
     */
    protected function getImage($data = 'testId')
    {
        return new Image($data);
    }

    /**
     * get Gallery
     *
     * @param  string  $data id album
     * @return Gallery
     */
    protected function getGallery($data = 'testId')
    {
        return new Gallery($data);
    }

    /**
     * @return \FDevs\Photatoes\Adapter\AdapterInterface
     */
    protected function getMockAdapter()
    {
        $image = $this->getImage();
        $meta = $this->getMeta('image', 50, 50);
        $testGallery = $this->getGallery();
        $testGallery->setCount(1);
        $testGallery->setImages(array($image));
        $testGallery->setName('test album');
        $testGallery->setUpdatedAt(new \DateTime());

        $adapter = $this->getMock('FDevs\Photatoes\Adapter\AdapterInterface');

        $adapter->expects($this->any())
            ->method('getListGallery')
            ->will($this->returnValue(array($testGallery)));

        $adapter->expects($this->any())
            ->method('saveImage')
            ->will($this->returnValue(true));

        $adapter->expects($this->any())
            ->method('getGallery')
            ->with($this->isInstanceOf('FDevs\Photatoes\Gallery'))
            ->will($this->returnCallback(function ($val) use ($testGallery) {
                $val->setCount($testGallery->getCount());
                $val->setImages($testGallery->getImages());
                $val->setName($testGallery->getName());
                $val->setUpdatedAt($testGallery->getUpdatedAt());
            }));

        $adapter->expects($this->any())
            ->method('getCover')
            ->with($this->isInstanceOf('FDevs\Photatoes\Gallery'))
            ->will($this->returnCallback(function ($val) use ($meta) {
                $val->addCover($meta);
            }));

        $adapter->expects($this->any())
            ->method('getImagesGallery')
            ->with($this->isInstanceOf('FDevs\Photatoes\Gallery'))
            ->will($this->returnCallback(function ($val) {
                $val->addImage(new Image('test2Id'));
            }));

        return $adapter;
    }

}
