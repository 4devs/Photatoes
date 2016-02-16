<?php
/**
 * @author    Andrey Samusev <andrey@4devs.org>
 * @copyright andrey 9/10/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Adapter;

use FDevs\Photatoes\Gallery;
use FDevs\Photatoes\Image;
use FDevs\Photatoes\Manager;
use FDevs\Photatoes\Meta;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class YandexAdapter implements AdapterInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * init
     *
     * @param string          $user
     * @param ClientInterface $client
     */
    public function __construct($user, ClientInterface $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
        $this->client->setConfig(array('users' => $user));
        $this->client->setDefaultOption('headers', array('Accept' => 'application/json'));
        $this->client->setBaseUrl($this->getBaseUrl());
    }

    /**
     * {@inheritDoc}
     */
    public function getImage(Image $image)
    {
        $photo = $this->getDataByType($image->getId());

        return $this->mappingImage($image, $photo);
    }

    /**
     * {@inheritDoc}
     */
    public function getGallery(Gallery $album)
    {
        $data = $this->getDataByType($album->getId(), 'album');
        $this->setGallery($data, $album);

        return $album;
    }

    /**
     * {@inheritDoc}
     */
    public function getCover(Gallery $album)
    {
        $data = $this->getDataByType($album->getId(), 'album');
        if (isset($data['links']['cover'])) {
            $idImage = array_slice(explode('/', $data['links']['cover']), -2, 1);
            $data = $this->getDataByType($idImage[0]);
            $this->setListCover($data['img'], $album);
        }

        return $album;
    }

    /**
     * {@inheritDoc}
     */
    public function getImagesGallery(Gallery $album)
    {
        $data = $this->getDataByType($album->getId(), 'albumPhotos');
        if (isset($data['entries'])) {
            foreach ($data['entries'] as $dataImage) {
                $image = new Image(substr(strrchr($dataImage['id'], ':'), 1));
                $this->mappingImage($image, $dataImage);
                $album->addImage($image);
            }
        }

        return $album;
    }

    /**
     * {@inheritDoc}
     */
    public function getListGallery(Manager $manager)
    {
        $albums = array();
        $data = $this->getDataByType('', 'albums');
        if (isset($data['entries'])) {
            foreach ($data['entries'] as $entry) {
                $albums[] = $this->setGallery($entry, new Gallery($this->getId($entry['id']), $manager));
            }
        }

        return $albums;
    }

    /**
     * {@inheritDoc}
     */
    public function saveImage(Image $image)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function saveGallery(Gallery $gallery)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getTagList()
    {
        $tags = array();
        $data = $this->getDataByType('', 'tags');
        if (isset($data['entries'])) {
            foreach ($data['entries'] as $tag) {
                $tags[] = $tag['title'];
            }
        }

        return $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function getImagesByTag($tag, Manager $manager = null)
    {
        $data = $this->getDataByType(rawurlencode($tag), 'tag');
        $images = array();
        if (isset($data['entries'])) {
            foreach ($data['entries'] as $img) {
                $image = new Image($this->getId($img['id']), $manager);
                $images[] = $this->mappingImage($image, $img);
            }
        }

        return $images;
    }

    /**
     * mapping Image
     *
     * @param Image $image
     * @param array $data
     *
     * @return Image
     */
    private function mappingImage(Image $image, array $data)
    {
        if (isset($data['summary'])) {
            $image->setDescription($data['summary']);
        }
        if (isset($data['title'])) {
            $image->setTitle($data['title'])
                ->setPublishAt(new \DateTime($data['published']))
                ->setUpdateAt(new \DateTime($data['updated']))
                ->setTags(array_keys($data['tags']));
            foreach ($data['img'] as $value) {
                $image->addMeta(new Meta($value['href'], $value['width'], $value['height']));
            }
        }

        return $image;
    }

    /**
     * get Id
     *
     * @param string $id
     *
     * @return string
     */
    private function getId($id)
    {
        $return = array_slice(explode(':', $id), -1, 1);

        return count($return) ? $return[0] : '';
    }

    /**
     * get Base Url
     *
     * @return string
     */
    private function getBaseUrl()
    {
        return 'http://api-fotki.yandex.ru/api/users/{users}';
    }

    /**
     * set Gallery
     *
     * @param  array   $data album
     * @param  Gallery $album
     *
     * @return Gallery
     */
    private function setGallery(array $data, Gallery $album)
    {
        if (isset($data['title'])) {
            $album->setName($data['title'])
                ->setCount(0)
                ->setUpdatedAt(new \DateTime($data['updated']));
            if (isset($data['imageCount'])) {
                $album->setCount($data['imageCount']);
            }
            if (isset($data['img'])) {
                $this->setListCover($data['img'], $album);
            }
        }

        return $album;
    }

    /**
     * get Api Url
     *
     * @param  string $type album|photo|albumPhotos|albums or base path
     * @param  string $key
     *
     * @return string
     */
    private function getUrl($type, $key)
    {
        switch ($type) {
            case 'album':
                $url = 'album/%s/';
                break;
            case 'photo':
                $url = 'photo/%s/';
                break;
            case 'albumPhotos':
                $url = 'album/%s/photos/';
                break;
            case 'albums':
                $url = 'albums/';
                break;
            case 'tags':
                $url = 'tags/';
                break;
            case 'tag':
                $url = 'tag/%s/photos/';
                break;
            default:
                $url = '/';
        }

        return sprintf($url, $key);
    }

    /**
     * set Cover List
     *
     * @param array   $listCover
     * @param Gallery $album
     */
    private function setListCover(array $listCover, Gallery $album)
    {
        foreach ($listCover as $image) {
            $album->addCover(new Meta($image['href'], $image['width'], $image['height']));
        }
    }

    /**
     * get Data By Type
     *
     * @param  string $key
     * @param  string $type
     *
     * @return array
     */
    private function getDataByType($key, $type = 'photo')
    {
        $url = $this->getUrl($type, $key);

        return $this->getData($url);
    }

    /**
     * get Data By Url
     *
     * @param string $url
     *
     * @return array
     */
    private function getData($url)
    {
        try {
            $request = $this->client->get($url);
            $response = $request->send();
            $decoder = new JsonDecode(true);
            $data = $decoder->decode($response->getBody(true), 'json');
            if (isset($data['links']['next'])) {
                $next = $this->getData($data['links']['next']);
                $data['entries'] = array_merge($data['entries'], $next['entries']);
            }
        } catch (\Exception $e) {
            $data = array();
        }

        return $data;
    }
}
