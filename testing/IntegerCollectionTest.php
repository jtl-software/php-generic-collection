<?php

declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: sleipi
 * Date: 1/7/20
 */

namespace JTL\Generic;

use PHPUnit\Framework\TestCase;

/**
 * @covers \JTL\Generic\IntegerCollection
 *
 * @uses \JTL\Generic\GenericCollection
 */
class IntegerCollectionTest extends TestCase
{
    public function testCanBeUsedWithInteger(): void
    {
        $col = new IntegerCollection();

        $col[] = -1;
        $col->add(1);

        $this->assertEquals(2, $col->count());
        $this->assertEquals(-1, $col[0]);
    }

    public function testFailWhenUsedWithNumericValues(): void
    {
        $col = new IntegerCollection();

        $this->expectException(\InvalidArgumentException::class);
        $col[] = "1";
    }
}
