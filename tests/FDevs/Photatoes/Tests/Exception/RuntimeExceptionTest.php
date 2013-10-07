<?php
/**
 * @author Andrey Samusev <Andrey.Samusev@exigenservices.com>
 * @copyright andrey 9/30/13
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FDevs\Photatoes\Tests\Exception;

use FDevs\Photatoes\Exception\RuntimeException;
use FDevs\Photatoes\Tests\TestCase;

class RuntimeExceptionTest extends TestCase
{
    public function testInstance()
    {
        $ex = new RuntimeException();
        $this->assertInstanceOf('\RuntimeException', $ex);
    }
}
