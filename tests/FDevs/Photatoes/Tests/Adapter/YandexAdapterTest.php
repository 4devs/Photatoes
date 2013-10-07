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
        $this->fullGallery($album);
    }

    public function testGetCover()
    {
        $album = $this->getGallery();
        $this->emptyGallery($album);

        $adapter = $this->getMockYandex();
        $adapter->getCover($album);
        $this->assertCount(7, $album->getCover());

        $album = $this->getGallery();
        $this->emptyGallery($album);
        $album->setManager($this->getManger($adapter));
        $this->assertCount(7, $album->getCover(true));
    }

    public function testGetImagesGallery()
    {
        $album = $this->getGallery();
        $this->emptyGallery($album);

        $adapter = $this->getMockYandex();
        $adapter->getImagesGallery($album);
        $this->assertCount(2, $album->getImages());
        $this->assertInstanceOf('FDevs\Photatoes\Image', current($album->getImages()));

        $album = $this->getGallery();
        $this->emptyGallery($album);
        $album->setManager($this->getManger($adapter));
        $this->assertCount(2, $album->getImages());
        $this->assertInstanceOf('FDevs\Photatoes\Image', current($album->getImages()));
    }

    public function testGetListGallery()
    {
        $adapter = $this->getMockYandex();
        $manager = $this->getManger();
        $listGallery = $adapter->getListGallery($manager);
        $this->assertCount(4, $listGallery);
        $this->assertInternalType('array', $listGallery);
        $this->assertInstanceOf('FDevs\Photatoes\Gallery', current($listGallery));
    }

    public function testSave()
    {
        $adapter = $this->getMockYandex();
        $this->assertFalse($adapter->saveGallery($this->getGallery()));
        $this->assertFalse($adapter->saveImage($this->getImage()));
    }

    /**
     * test Empty Gallery
     *
     * @param Gallery $album
     */
    private function emptyGallery(Gallery $album)
    {
        $this->assertCount(0, $album->getImages());
        $this->assertCount(0, $album->getCover());
        $this->assertEquals('testId', $album->getId());
        $this->assertNull($album->getName());
        $this->assertNull($album->getUpdatedAt());
        $this->assertEquals(0, $album->getCount());
    }

    /**
     * test Full Gallery
     *
     * @param Gallery $album
     */
    private function fullGallery(Gallery $album)
    {
        $this->assertCount(0, $album->getImages());
        $this->assertCount(2, $album->getCover());
        $this->assertEquals('testId', $album->getId());
        $this->assertInstanceOf('DateTime', $album->getUpdatedAt());
        $this->assertEquals('Путешествия', $album->getName());
        $this->assertEquals(2, $album->getCount());
    }

    /**
     * test Empty Image
     *
     * @param Image $photo
     */
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

    /**
     * test Full Image
     *
     * @param Image $photo
     */
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
     * get Mock Adapter
     *
     * @return \FDevs\Photatoes\Adapter\YandexAdapter
     */
    private function getMockYandex()
    {

        $adapter = new YandexAdapter('testUser', $this->getMockClient());

        return $adapter;
    }

    /**
     * get Guzzle Http Client
     *
     * @return \Guzzle\Http\Client
     */
    private function getMockClient()
    {
        $client = $this->getMock('Guzzle\Http\Client');
        $client->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($url) {
                $data = $this->getData();

                return isset($data[$url]) ? $this->getRequest($data[$url]) : $this->getRequest('{}');
            }));

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
            'album/testId/' => '{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/170471\/","cover":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/coverId\/","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/photos\/","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:170471","imageCount":2,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXS","height":75},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_S","height":113}},"title":"Путешествия","author":"alekna","published":"2011-06-06T11:36:53Z"}',
            'photo/coverId/' => '{"edited":"2012-07-02T11:56:21Z","updated":"2012-07-02T11:56:21Z","xxx":false,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXS","height":75},"XL":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XL","height":453},"M":{"width":300,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_M","height":225},"L":{"width":500,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_L","height":375},"XXXS":{"width":50,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXXS","height":50},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_S","height":113},"XS":{"width":100,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XS","height":75},"orig":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","bytesize":149497,"height":453}},"links":{"album":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/","editMedia":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/view\/471435\/","edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/"},"tags":{},"title":"Flowers","summary":"sea","access":"public","disableComments":false,"authors":[{"name":"alekna","uid":"14733932"}],"hideOriginal":false,"author":"alekna","geo":{"zoomlevel":"12","type":"Point","maptype":"schema","coordinates":"27.254145992919803 33.80939232185483"},"id":"urn:yandex:fotki:alekna:photo:471435","published":"2011-07-01T09:10:27Z"}',
            'album/testId/photos/' => '{"updated":"2013-07-16T23:54:33Z","author":"alekna","links":{"self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/photos\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/170471\/"},"title":"Путешествия","authors":[{"name":"alekna","uid":"14733932"}],"entries":[{"edited":"2012-07-02T11:56:21Z","updated":"2012-07-02T11:56:21Z","xxx":false,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXS","height":75},"XL":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XL","height":453},"M":{"width":300,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_M","height":225},"L":{"width":500,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_L","height":375},"XXXS":{"width":50,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXXS","height":50},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_S","height":113},"XS":{"width":100,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XS","height":75},"orig":{"width":604,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","bytesize":149497,"height":453}},"links":{"album":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/?format=json","editMedia":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_orig","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/view\/471435\/","edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/?format=json"},"tags":{},"title":"Flowers","summary":"sea","access":"public","disableComments":false,"authors":[{"name":"alekna","uid":"14733932"}],"hideOriginal":false,"author":"alekna","geo":{"zoomlevel":"12","type":"Point","maptype":"schema","coordinates":"27.254145992919803 33.80939232185483"},"id":"urn:yandex:fotki:alekna:photo:471435","published":"2011-07-01T09:10:27Z"},{"edited":"2011-07-05T07:47:25Z","updated":"2011-07-05T07:47:25Z","xxx":false,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_XXS","height":75},"XL":{"width":581,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_XL","height":480},"M":{"width":300,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_M","height":248},"L":{"width":500,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_L","height":413},"XXXS":{"width":50,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_XXXS","height":50},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_S","height":124},"XS":{"width":100,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_XS","height":83},"orig":{"width":581,"href":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_orig","bytesize":70211,"height":480}},"links":{"album":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/?format=json","editMedia":"http:\/\/img-fotki.yandex.ru\/get\/5211\/alekna.2\/0_735bd_bc4bc35_orig","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/472509\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/view\/472509\/","edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/472509\/?format=json"},"tags":{},"title":"hungary","summary":"Нас показали!","access":"public","disableComments":false,"authors":[{"name":"alekna","uid":"14733932"}],"hideOriginal":false,"author":"alekna","geo":{"zoomlevel":"7","type":"Point","maptype":"schema","coordinates":"47.54326446913183 19.257939448580146"},"id":"urn:yandex:fotki:alekna:photo:472509","published":"2011-07-04T08:54:24Z"}],"id":"urn:yandex:fotki:alekna:album:170471:photos","imageCount":2}',
            'albums/' => '{"updated":"2013-07-16T23:54:33Z","links":{"self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/albums\/","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/albums\/"},"title":"alekna на Яндекс.Фотках","authors":[{"name":"alekna","uid":"14733932"}],"entries":[{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197816\/?format=json","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197816\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/197816\/","cover":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/657065\/?format=json","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197816\/photos\/?format=json","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197816\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:197816","imageCount":4,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/6114\/14733932.2\/0_a06a9_1d9e7bf_-1-XXS","height":75},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/6114\/14733932.2\/0_a06a9_1d9e7bf_-1-S","height":112}},"title":"Природа","author":"alekna","published":"2012-07-09T11:46:31Z"},{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197663\/?format=json","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197663\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/197663\/","cover":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/656634\/?format=json","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197663\/photos\/?format=json","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/197663\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:197663","imageCount":1,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5806\/14733932.2\/0_a04fa_2f68a5db_XXS","height":75},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5806\/14733932.2\/0_a04fa_2f68a5db_S","height":110}},"title":"Животные","author":"alekna","published":"2012-07-06T07:54:46Z"},{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/?format=json","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/photos\/?format=json","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/172007\/","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/172007\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:172007","imageCount":2,"title":"Лето","author":"alekna","protected":true,"published":"2011-06-30T12:27:43Z"},{"edited":"2013-07-16T23:54:33Z","updated":"2013-07-16T23:54:33Z","links":{"edit":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/?format=json","self":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/?format=json","alternate":"http:\/\/fotki.yandex.ru\/users\/alekna\/album\/170471\/","cover":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/photo\/471435\/?format=json","photos":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/photos\/?format=json","ymapsml":"http:\/\/api-fotki.yandex.ru\/api\/users\/alekna\/album\/170471\/photos.ymapsml\/"},"authors":[{"name":"alekna","uid":"14733932"}],"id":"urn:yandex:fotki:alekna:album:170471","imageCount":2,"img":{"XXS":{"width":75,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_XXS","height":75},"S":{"width":150,"href":"http:\/\/img-fotki.yandex.ru\/get\/5907\/alekna.2\/0_7318b_2136a628_S","height":113}},"title":"Путешествия","author":"alekna","published":"2011-06-06T11:36:53Z"}],"author":"alekna","id":"urn:yandex:fotki:alekna:albums"}',
        );
    }
}
