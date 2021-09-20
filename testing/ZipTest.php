<?php

declare(strict_types=1);

namespace JTL\Generic;

use PHPUnit\Framework\TestCase;

/**
 * Class ZipTest
 *
 * @covers \JTL\Generic\Zip
 */
class ZipTest extends TestCase
{
    public function testCanCreateTuple(): void
    {
        $first = random_int(1, 100000);
        $second = random_int(1, 100000);
        $zip = new Zip($first, $second);

        $this->assertEquals($first, $zip->getLeft());
        $this->assertEquals($second, $zip->getRight());
    }
}
