<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/10/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes;

class Image implements \Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title = null;

    /**
     * @var string
     */
    private $description = null;

    /**
     * @var \DateTime
     */
    private $publishAt = null;

    /**
     * @var \DateTime
     */
    private $updateAt = null;

    /**
     * @var array
     */
    private $tags = array();

    /**
     * @var Meta[]
     */
    private $img = array();

    /**
     * @var Manager
     */
    private $manager = null;

    /**
     * init
     *
     * @param string  $id      id image
     * @param Manager $manager
     */
    public function __construct($id, Manager $manager = null)
    {
        $this->id = $id;
        if ($manager) {
            $this->setManager($manager);
            $this->manager->getAdapter()->getImage($this);
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
     * save Image to the server
     *
     * @return bool
     */
    public function save()
    {
        return $this->manager ? $this->manager->getAdapter()->saveImage($this) : false;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * add Image
     *
     * @param Meta $file
     * @return $this
     */
    public function addMeta(Meta $file)
    {
        $this->img[$file->getSize()] = $file;

        return $this;
    }

    /**
     * get Img
     * @param  string    $size
     * @return Meta|null
     */
    public function get($size)
    {
        $size = ImageSize::get($size);
        if (isset($this->img[$size])) {
            return $this->img[$size];
        }

        return null;
    }

    /**
     * @return \FDevs\Photatoes\Meta[]
     */
    public function all()
    {
        return $this->img;
    }

    /**
     * @param \DateTime $publishAt
     * @return $this
     */
    public function setPublishAt(\DateTime $publishAt)
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishAt()
    {
        return $this->publishAt;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set Update At
     *
     * @param \DateTime $updateAt
     * @return $this
     */
    public function setUpdateAt(\DateTime $updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * get Update At
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * add Tag
     *
     * @param string $tag
     * @return $this
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * has Image in Server
     *
     * @return bool
     */
    public function has()
    {
        return !empty($this->publishAt);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $data = array(
            $this->id,
            $this->title,
            $this->description,
            $this->tags,
            $this->publishAt,
            $this->updateAt,
            $this->img
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
            $this->title,
            $this->description,
            $this->tags,
            $this->publishAt,
            $this->updateAt,
            $this->img
            ) = $data;
    }
}
