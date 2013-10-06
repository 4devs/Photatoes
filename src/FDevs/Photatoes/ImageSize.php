<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/18/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes;


class ImageSize
{
    const SIZE_ORIG = 'orig';
    const SIZE_XXS = 'XXS';
    const SIZE_XL = 'XL';
    const SIZE_XXL = 'XXL';
    const SIZE_M = 'M';
    const SIZE_L = 'L';
    const SIZE_XXXS = 'XXXS';
    const SIZE_S = 'S';
    const SIZE_XS = 'XS';

    private static $allowedSize = array(
        self::SIZE_L => 'L',
        self::SIZE_M => 'M',
        self::SIZE_ORIG => 'orig',
        self::SIZE_S => 'S',
        self::SIZE_XL => 'XL',
        self::SIZE_XXL => 'XXL',
        self::SIZE_XS => 'XS',
        self::SIZE_XXS => 'XXS',
        self::SIZE_XXXS => 'XXXS',
    );

    /**
     * @var array
     */
    private static $size = array(
        self::SIZE_L => array(
            'width' => 500,
            'height' => 500,
        ),
        self::SIZE_M => array(
            'width' => 300,
            'height' => 300,
        ),
        self::SIZE_ORIG => array(
            'width' => 0,
            'height' => 0,
        ),
        self::SIZE_S => array(
            'width' => 150,
            'height' => 150,
        ),
        self::SIZE_XL => array(
            'width' => 800,
            'height' => 800,
        ),
        self::SIZE_XXL => array(
            'width' => 1024,
            'height' => 765,
        ),
        self::SIZE_XS => array(
            'width' => 100,
            'height' => 75,
        ),
        self::SIZE_XXS => array(
            'width' => 75,
            'height' => 75,
        ),
        self::SIZE_XXXS => array(
            'width' => 50,
            'height' => 50,
        ),
    );

    /**
     * get name allowed return
     *
     * @param string $name
     * @return string|null
     */
    public static function get($name)
    {
        $name = (strtolower($name) === self::SIZE_ORIG) ? self::SIZE_ORIG : strtoupper($name);

        return isset(self::$allowedSize[$name]) ? self::$allowedSize[$name] : null;
    }

    /**
     * has size
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {
        return isset(self::$allowedSize[$name]);
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function getSize($width = 0, $height = 0)
    {
        $size = 'orig';
        $array = array_filter(self::$size, function ($var) use ($width, $height) {
            return ($var['width'] >= $width && $var['height'] >= $height);
        });
        if (count($array)) {
            asort($array);
            $size = key($array);
        }

        return $size;
    }
}