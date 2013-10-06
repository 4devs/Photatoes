<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/10/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes;

use FDevs\Photatoes\Adapter\AdapterInterface;

class Manager
{

    /**
     * @var Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * init
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * get Adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * get Image
     *
     * @param string $key
     * @param bool $create
     * @return Image
     */
    public function getImage($key, $create = false)
    {
        return $this->createImage($key);
    }

    /**
     * get Gallery
     *
     * @param string $id
     * @return Gallery
     */
    public function getGallery($id)
    {
        return new Gallery($id, $this);
    }

    /**
     * get Gallery List
     *
     * @return array
     */
    public function getGalleryList()
    {
        return $this->adapter->getListGallery($this);
    }

    /**
     * create Image
     *
     * @param string $key
     * @return Image
     */
    private function createImage($key)
    {
        return new Image($key, $this);
    }

}