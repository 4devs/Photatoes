<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/12/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes;

class Gallery implements \Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var \DateTime
     */
    private $updatedAt = null;

    /**
     * @var Meta[]
     */
    private $cover = array();

    /**
     * @var Image[]
     */
    private $images = array();

    /**
     * @var Manager
     */
    private $manager = null;

    /**
     * init
     *
     * @param string $id
     * @param Manager|null $manager
     */
    public function __construct($id, Manager $manager = null)
    {
        $this->id = $id;
        if ($manager) {
            $this->setManager($manager);
            $this->reload();
        }
    }

    /**
     * set Manager
     *
     * @param Manager $manager
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * reload Gallery
     *
     * @param bool $all
     * @return $this
     * @throws Exception\RuntimeException
     */
    public function reload($all = false)
    {
        if ($this->manager) {
            $this->manager->getAdapter()->getGallery($this);
            if ($all) {
                $this->getCover(true);
                $this->getImages(true);
            }
        }

        return $this;
    }

    /**
     * get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * set Images
     *
     * @param \FDevs\Photatoes\Image[] $images
     * @return $this
     */
    public function setImages(array $images)
    {
        $this->images = array();
        foreach ($images as $image) {
            $this->addImage($image);
        }
        $this->count = count($this->images);

        return $this;
    }

    /**
     * get Images
     *
     * @param bool $reload
     * @return \FDevs\Photatoes\Image[]
     */
    public function getImages($reload = false)
    {
        if ((!count($this->images) || $reload) && $this->manager) {
            $this->manager->getAdapter()->getImagesGallery($this);
        }

        return $this->images;
    }

    /**
     * add Image
     *
     * @param Image $image
     * @return $this
     */
    public function addImage(Image $image)
    {
        if (!isset($this->images[$image->getId()])) {
            $this->count++;
        }
        $this->images[$image->getId()] = $image;

        return $this;
    }

    /**
     * set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set Count
     *
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * get Count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * add Cover
     *
     * @param Meta $meta
     * @return $this
     */
    public function addCover(Meta $meta)
    {
        $this->cover[$meta->getSize()] = $meta;

        return $this;
    }

    /**
     * get Cover
     *
     * @param bool $reload
     * @return \FDevs\Photatoes\Meta[]
     */
    public function getCover($reload = false)
    {
        if ((!count($this->cover) || $reload) && $this->manager) {
            $this->manager->getAdapter()->getCover($this);
        }

        return $this->cover;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * save Gallery
     *
     * @return bool
     */
    public function save()
    {
        return $this->manager ? $this->manager->getAdapter()->saveGallery($this) : false;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $this->reload(true);
        $data = array(
            $this->id,
            $this->count,
            $this->updatedAt,
            $this->name,
            $this->cover,
            $this->images
        );

        return serialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        list(
            $this->id,
            $this->count,
            $this->updatedAt,
            $this->name,
            $this->cover,
            $this->images
            ) = $data;
    }

} 