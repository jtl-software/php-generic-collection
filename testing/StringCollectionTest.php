<?php declare(strict_types=1);
/**
 * This File is part of JTL-Software
 *
 * User: sleipi
 * Date: 1/7/20
 */

namespace JTL\Generic;


use PHPUnit\Framework\TestCase;

/**
 * @covers \JTL\Generic\StringCollection
 *
 * @uses \JTL\Generic\GenericCollection
 */
class StringCollectionTest extends TestCase
{

    public function testCanBeUsedWithStrings()
    {
        $col = new StringCollection();

        $col[] = "foo";
        $col->add('bar');

        $this->assertEquals(2, $col->count());
        $this->assertEquals('bar', $col[1]);
    }

    public function testFailWhenUsedWithNumericValues()
    {
        $col = new StringCollection();

        $this->expectException(\InvalidArgumentException::class);
        $col[] = 1;
    }
}
