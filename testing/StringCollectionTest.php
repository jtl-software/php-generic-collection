<?php

declare(strict_types=1);

namespace JTL\Generic;

use PHPUnit\Framework\TestCase;

/**
 * @covers \JTL\Generic\StringCollection
 *
 * @uses \JTL\Generic\GenericCollection
 */
class StringCollectionTest extends TestCase
{
    public function testCanBeUsedWithStrings(): void
    {
        $col = new StringCollection();

        $col[] = "foo";
        $col->add('bar');

        $this->assertEquals(2, $col->count());
        $this->assertEquals('bar', $col[1]);
    }

    public function testFailWhenUsedWithNumericValues(): void
    {
        $col = new StringCollection();

        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        $col[] = 1;
    }
}
