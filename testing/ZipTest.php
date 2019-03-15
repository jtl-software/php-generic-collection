<?php
/**
 * User: mbrandt
 * Date: 15/03/19
 */

use JTL\Generic\Zip;
use PHPUnit\Framework\TestCase;

/**
 * Class ZipTest
 *
 * @covers \JTL\Generic\Zip
 */
class ZipTest extends TestCase
{

    public function testCanCreateTuple()
    {
        $first = random_int(1, 100000);
        $second = random_int(1, 100000);
        $zip = new Zip($first, $second);

        $this->assertEquals($first, $zip->getLeft());
        $this->assertEquals($second, $zip->getRight());
    }
}
