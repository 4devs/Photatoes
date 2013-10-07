<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/10/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Adapter;

use FDevs\Photatoes\Image;
use FDevs\Photatoes\Gallery;
use FDevs\Photatoes\Manager;

interface AdapterInterface
{
    /**
     * get Photo
     *
     * @param  Image                  $image
     * @return \FDevs\Photatoes\Image
     */
    public function getImage(Image $image);

    /**
     * put image on server
     *
     * @param  Image   $image
     * @return boolean
     */
    public function saveImage(Image $image);

    /**
     * mapping Image
     *
     * @param  Image $image
     * @param  mixed $data
     * @return Image
     */
    public function mappingImage(Image $image, $data);

    /**
     * get Gallery
     *
     * @param  Gallery $album
     * @return Gallery
     */
    public function getGallery(Gallery $album);

    /**
     * get Cover
     *
     * @param  Gallery $album
     * @return Gallery
     */
    public function getCover(Gallery $album);

    /**
     * save Gallery
     *
     * @param  Gallery $album
     * @return boolean
     */
    public function saveGallery(Gallery $album);

    /**
     * get Images Gallery
     *
     * @param  Gallery $album
     * @return Gallery
     */
    public function getImagesGallery(Gallery $album);

    /**
     * get Galleries
     *
     * @param  Manager $manager
     * @return mixed
     */
    public function getListGallery(Manager $manager);

}
