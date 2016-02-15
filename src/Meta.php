<?php
/**
 * @author    Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/16/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes;

class Meta implements \Serializable
{
    /**
     * @var int
     */
    private $width = 0;

    /**
     * @var int
     */
    private $height = 0;

    /**
     * @var string
     */
    private $href = '';

    /**
     * @var string
     */
    private $size = '';

    /**
     * init
     *
     * @param string $href
     * @param int    $height
     * @param int    $width
     */
    public function __construct($href, $width = 0, $height = 0)
    {
        $this->height = $height;
        $this->href = $href;
        $this->width = $width;
        $this->setSize(ImageSize::getSize($width, $height));
    }

    /**
     * to String
     *
     * @return string
     */
    public function __toString()
    {
        return $this->href;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        $data = array(
            $this->href,
            $this->width,
            $this->height,
            $this->size
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
            $this->href,
            $this->width,
            $this->height,
            $this->size
            ) = $data;
    }

    /**
     * set Size
     *
     * @param string $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        if ($size = ImageSize::get($size)) {
            $this->size = $size;
        }

        return $this;
    }

    /**
     * get Size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $href
     *
     * @return $this
     */
    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * set Href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * set Width
     *
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * get Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
