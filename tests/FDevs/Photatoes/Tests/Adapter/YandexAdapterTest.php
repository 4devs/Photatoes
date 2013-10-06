<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 10/3/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests\Adapter;

use FDevs\Photatoes\Adapter\YandexAdapter;
use FDevs\Photatoes\Gallery;
use FDevs\Photatoes\Image;
use FDevs\Photatoes\Tests\TestCase;

class YandexAdapterTest extends TestCase
{
    public function testGetPhoto()
    {
        $adapter = $this->getMockYandex();
        $image = $this->getImage();
        $this->emptyImage($image);
        $photo = $adapter->getImage($image);
        $this->fullImage($photo);
    }

    public function testMappingImage()
    {
        $img = $this->getImage();
        $this->emptyImage($img);
        $adapter = $this->getMockYandex();
        $data = $this->getData();
        $adapter->mappingImage($img, json_decode($data['photo/testId/'], true));
        $this->fullImage($img);

    }

    public function testGetGallery()
    {
        $album = $this->getGallery();
        $this->emptyGallery($album);
        $adapter = $this->getMockYandex();
        $adapter->getGallery($album);
//        var_dump($album);
    }

    private function emptyGallery(Gallery $album)
    {
        $this->assertCount(0, $album->getImages());
        $this->assertCount(0, $album->getCover());
        $this->assertEquals('testId', $album->getId());
        $this->assertNull($album->getName());
        $this->assertNull($album->getUpdatedAt());
        $this->assertEquals(0, $album->getCount());

    }

    private function emptyImage(Image $photo)
    {
        $this->assertCount(0, $photo->all());
        $this->assertCount(0, $photo->getTags());
        $this->assertEquals('testId', $photo->getId());
        $this->assertNull($photo->getTitle());
        $this->assertNull($photo->getDescription());
        $this->assertNull($photo->getUpdateAt());
        $this->assertNull($photo->getPublishAt());

    }

    private function fullImage(Image $photo)
    {
        $this->assertCount(7, $photo->all());
        $this->assertEquals('testId', $photo->getId());
        $this->assertEquals('Flowers', $photo->getTitle());
        $this->assertEquals('sea', $photo->getDescription());
        $this->assertInstanceOf('DateTime', $photo->getUpdateAt());
        $this->assertInstanceOf('DateTime', $photo->getPublishAt());

    }

    /**
     * @return \FDevs\Photatoes\Adapter\YandexAdapter
     */
    private function getMockYandex()
    {

        $adapter = new YandexAdapter('testUser', $this->getMockClient());

        return $adapter;
    }

    private function getMockClient()
    {
        $client = $this->getMock('Guzzle\Http\Client');
        $data = $this->getData();
        $with = array();
        $will = array();
        foreach ($data as $key => $value) {
            $with[] = $this->equalTo($key);
            $will[] = $this->getRequest($value);

//            $client->expects($this->any())
//                ->method('get')
//                ->with($this->equalTo($key))
//                ->will($this->returnValue($this->getRequest($value)));
        }
        $client->expects($this->any())
            ->method('get')
            ->with($with)
            ->will($this->returnValueMap($will));

        return $client;
    }

    private function getRequest($response)
    {
        $responsePhoto = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $requestPhoto = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $responsePhoto->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($response));

        $requestPhoto->expects($this->any())
            ->method('send')
            ->will($this->returnValue($responsePhoto));

        return $requestPhoto;
    }

    private function getData()
    {
        return array(
            'photo/testId/' => '{"edited":"2012-07-02T11:56:21Z","updated":"2012-07-02T11:56:21Z","xxx":false,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXS","height":75},"XL":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XL","height":453},"M":{"width":300,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_M","height":225},"L":{"width":500,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_L","height":375},"XXXS":{"width":50,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXXS","height":50},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_S","height":113},"XS":{"width":100,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XS","height":75},"orig":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","bytesize":149497,"height":453}},"links":{"album":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/","editMedia":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/view\/471435\/","edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/"},"tags":{},"title":"Flowers","summary":"sea","access":"public","disableComments":false,"authors":[{"name":"alekna","uid":"14733932"}],"hideOriginal":false,"author":"alekna","geo":{"zoomlevel":"12","type":"Point","maptype":"schema","coordinates":"27.254145992919803 33.80939232185483"},"id":"urn:yandex:fotki:alekna:photo:471435","published":"2011-07-01T09:10:27Z"}',
//            'album/testId/' => '{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/photos\/","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/172007\/","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:172007","imageCount":2,"title":"Лето","author":"alekna","protected":true,"published":"2011-06-30T12:27:43Z"}',
        );
    }
}