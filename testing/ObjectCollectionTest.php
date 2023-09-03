<?php

declare(strict_types=1);

use JTL\Generic\TestItem;
use PHPUnit\Framework\TestCase;

/**
 * @covers \JTL\Generic\ObjectCollection
 *
 * @uses \JTL\Generic\GenericCollection
 */
class ObjectCollectionTest extends TestCase
{
    public function testCanBeUsedWithObject(): void
    {
        $col = new \JTL\Generic\ObjectCollection(TestItem::class);

        $col->add(new TestItem(3));

        $this->assertEquals(1, $col->count());
        $this->assertEquals(3, $col[0]->a);
        $this->assertEquals(3, $col->getArray()[0]->a);
    }

    public function testCanBeUsedWithObjectViaFrom(): void
    {
        $col = \JTL\Generic\ObjectCollection::from(new TestItem(3), new TestItem(2));

        $this->assertEquals(2, $col->count());
        $this->assertEquals(3, $col[0]->a);
        $this->assertEquals(3, $col->getArray()[0]->a);
    }

    public function testFailWhenUsedWithInvalidClassNameValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        new \JTL\Generic\ObjectCollection('FooNonExists');
    }

    public function testFailWhenUsedWithInvalidObject(): void
    {
        $col = new \JTL\Generic\ObjectCollection(TestItem::class);

        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        $col[] = new \DateTimeImmutable();
    }

    public function testFailWhenUsedWithInvalidObjectV2(): void
    {
        $col = new \JTL\Generic\ObjectCollection(TestItem::class);

        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        $col->add(new \DateTimeImmutable());
    }

    public function testFailWhenUsedWithInvalidObjectV3(): void
    {
        $col = new \JTL\Generic\ObjectCollection(TestItem::class);

        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        $col->addItemList([new \DateTimeImmutable()]);
    }

    public function testFailWhenUsedWithNumericValuesWithFrom(): void
    {
        $col = \JTL\Generic\ObjectCollection::from(new TestItem(3));

        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line | fail test | is ok here */
        $col[] = new \DateTimeImmutable();
    }
}
