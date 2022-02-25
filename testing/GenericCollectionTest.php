<?php

declare(strict_types=1);

namespace JTL\Generic;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class GenericCollectionTest
 *
 * @covers \JTL\Generic\GenericCollection
 *
 * @uses   \JTL\Generic\Zip
 * @uses   \JTL\Generic\ZippedCollection
 */
class GenericCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection->add($testItem1);

        $this->assertEquals(123, $collection[0]->a);
    }

    public function testAddException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new TestCollection();
        $collection->add(1);
    }

    public function testCanCreateCollectionWithType(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertEquals(123, $collection[0]->a);
    }

    public function testCanCreateCollectionWithTypeFromArgumentList(): void
    {
        $testItem1 = new TestItem(123);
        $testItem2 = new TestItem(456);
        $collection = TestCollection::from($testItem1, $testItem2);

        $this->assertEquals(123, $collection[0]->a);
        $this->assertEquals(456, $collection[1]->a);
    }

    public function testFailIfSetWithWrongType(): void
    {
        $collection = new TestCollection();
        $this->expectException(InvalidArgumentException::class);
        $collection[] = random_int(1, 100000);
    }

    public function testCanIterate(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        foreach ($collection as $item) {
            $this->assertEquals(123, $item->a);
        }
    }

    public function testCanCheckIfOffsetExists(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertTrue(isset($collection[0]));
    }

    public function testCanUnset(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        unset($collection[0]);
        $this->assertFalse(isset($collection[0]));
    }

    public function testCanGetType(): void
    {
        $collection = new TestCollection();

        $this->assertEquals($collection->getType(), TestItem::class);
    }

    public function testCanCountElements(): void
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertEquals(1, $collection->count());
    }

    public function testCanAddMultipleFromArray(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $this->assertEquals(3, $collection->count());
        $this->assertEquals(111, $collection[0]->a);
        $this->assertEquals(222, $collection[1]->a);
        $this->assertEquals(333, $collection[2]->a);
    }

    public function testFailIfAddMultipleFromArrayHasWrongType(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItemArray = [$testItem1, $testItem2, 'TEST'];
        $collection = new TestCollection();
        $this->expectException(InvalidArgumentException::class);
        $collection->addItemList($testItemArray);
    }

    public function testCanDoEach(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $each = function ($item) {
            $this->assertInstanceOf(TestItem::class, $item);
            $item->a += 1;
        };

        $collection->each($each);

        $this->assertEquals(112, $collection[0]->a);
        $this->assertEquals(223, $collection[1]->a);
        $this->assertEquals(334, $collection[2]->a);
    }

    public function testCanMap(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $map = function ($item) {
            $item->a = 666;
            return $item;
        };

        $collection->map($map);

        foreach ($collection as $item) {
            $this->assertEquals(666, $item->a);
        }
    }

    public function testCanFilter(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $filter = function ($item) {
            return $item->a > 200;
        };

        $collection->filter($filter);

        $this->assertEquals(2, $collection->count());
    }

    public function testCanClone(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $filter = function ($item) {
            return $item->a > 200;
        };

        $filteredCollection = $collection->clone()->filter($filter);

        $this->assertEquals(3, $collection->count());
        $testItem1->a = 444;
        $testItem2->a = 555;
        $this->assertEquals(222, $filteredCollection[1]->a);
        $this->assertEquals(333, $filteredCollection[2]->a);

        $collection[1]->a = 666;
        $collection[2]->a = 777;
        $this->assertEquals(666, $collection[1]->a);
        $this->assertEquals(777, $collection[2]->a);

        $this->assertEquals(2, $filteredCollection->count());
        $this->assertEquals(222, $filteredCollection[1]->a);
        $this->assertEquals(333, $filteredCollection[2]->a);
    }

    public function testCanChain(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $collection->chain($collection2);

        $this->assertEquals(6, $collection->count());
        $this->assertEquals(3, $collection2->count());
    }

    public function testFailChainIfTypesIncompatible(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem2(1000);
        $testItem2 = new TestItem2(2000);
        $testItem3 = new TestItem2(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection2();
        $collection2->addItemList($testItemArray);

        $this->expectExceptionMessage('Invalid type by collections');
        $this->expectException(InvalidArgumentException::class);
        $collection->chain($collection2);
    }

    public function testFailChainIfItemTypeIncompatible(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $collection2->map(function ($item) {
            if ($item->a === 1000) {
                return 'E';
            }

            return $item;
        });

        $this->expectException(InvalidArgumentException::class);
        $collection->chain($collection2);
    }

    public function testCanZip(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $zip = $collection->zip($collection2);

        $this->assertEquals(3, $zip->count());

        $this->assertEquals(111, $zip[0]->getLeft()->a);
        $this->assertEquals(1000, $zip[0]->getRight()->a);

        $this->assertEquals(222, $zip[1]->getLeft()->a);
        $this->assertEquals(2000, $zip[1]->getRight()->a);

        $this->assertEquals(333, $zip[2]->getLeft()->a);
        $this->assertEquals(3000, $zip[2]->getRight()->a);

        $this->assertEquals('JTL\Generic\TestCollection', $zip->getLeftOriginalClassName());
        $this->assertEquals('JTL\Generic\TestItem', $zip->getLeftOriginalItemType());
        $this->assertEquals('JTL\Generic\TestCollection', $zip->getRightOriginalClassName());
        $this->assertEquals('JTL\Generic\TestItem', $zip->getRightOriginalItemType());
    }

    public function testCanZipWithDifferentLengths(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItem4 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3, $testItem4];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $zip = $collection->zip($collection2);

        $this->assertEquals(3, $zip->count());

        $this->assertEquals(111, $zip[0]->getLeft()->a);
        $this->assertEquals(1000, $zip[0]->getRight()->a);

        $this->assertEquals(222, $zip[1]->getLeft()->a);
        $this->assertEquals(2000, $zip[1]->getRight()->a);

        $this->assertEquals(333, $zip[2]->getLeft()->a);
        $this->assertEquals(3000, $zip[2]->getRight()->a);
    }

    public function testFailZipIfOtherCollectionTypesIncompatible(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $collection2->map(function ($item) {
            if ($item->a === 1000) {
                return 'E';
            }

            return $item;
        });

        $this->expectExceptionMessage('Invalid type in other collection. Expected: ' . $collection2->getType());
        $this->expectException(InvalidArgumentException::class);
        $collection->zip($collection2);
    }

    public function testFailZipIfOwnCollectionTypesIncompatible(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection2 = new TestCollection();
        $collection2->addItemList($testItemArray);

        $collection->map(function ($item) {
            if ($item->a === 111) {
                return 'E';
            }

            return $item;
        });

        $this->expectExceptionMessage('Invalid type in collection. Expected: ' . $collection->getType());
        $this->expectException(InvalidArgumentException::class);
        $collection->zip($collection2);
    }

    public function testCanPartition(): void
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItem4 = new TestItem(1000);
        $testItem5 = new TestItem(2000);
        $testItem6 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3, $testItem4, $testItem5, $testItem6];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        /**
         * @var TestCollection $lt1000
         * @var TestCollection $gte1000
         */
        [$lt1000, $gte1000] = $collection->partition(function ($item) {
            return $item->a < 1000;
        });

        $this->assertEquals(3, $lt1000->count());
        $this->assertEquals(3, $gte1000->count());
        $this->assertEquals(TestItem::class, $lt1000->getType());
        $this->assertEquals(TestItem::class, $gte1000->getType());

        $this->assertEquals(111, $lt1000[0]->a);
        $this->assertEquals(222, $lt1000[1]->a);
        $this->assertEquals(333, $lt1000[2]->a);

        $this->assertEquals(1000, $gte1000[0]->a);
        $this->assertEquals(2000, $gte1000[1]->a);
        $this->assertEquals(3000, $gte1000[2]->a);
    }

    public function testCanReduce(): void
    {
        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $this->assertEquals(6000, $collection->reduce(function ($carry, $item) {
            return $carry + $item->a;
        }));
    }

    public function testCanTestAll(): void
    {
        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $this->assertTrue($collection->all(function ($item) {
            return $item->a > 900;
        }));

        $this->assertFalse($collection->all(function ($item) {
            return $item->a > 9000;
        }));
    }

    public function testCanTestAny(): void
    {
        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $this->assertTrue($collection->any(function ($item) {
            return $item->a > 2000;
        }));

        $this->assertFalse($collection->any(function ($item) {
            return $item->a > 9000;
        }));
    }

    public function testCanFind(): void
    {
        $testItem1 = new TestItem(1000);
        $testItem2 = new TestItem(2000);
        $testItem3 = new TestItem(3000);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $this->assertEquals($testItem3, $collection->find(function ($item) {
            return $item->a > 2000;
        }));

        $this->assertNull($collection->find(function ($item) {
            return $item->a > 9000;
        }));
    }

    public function testCanOmitTypeInConstructor(): void
    {
        $testItem1 = new TestItem(1000);
        $testCollection = new TestCollection3();
        $testCollection[] = $testItem1;
        $this->assertCount(1, $testCollection);
    }

    public function testCheckTypes(): void
    {
        $subject = new TestCollection4();
        $this->assertTrue($subject->checkType(''));
        $this->assertTrue($subject->checkType([]));
        $this->assertTrue($subject->checkType(null));

        $subject = new TestCollection4('int');
        $this->assertTrue($subject->checkType(1));

        $subject = new TestCollection4('string');
        $this->assertTrue($subject->checkType('12'));

        $subject = new TestCollection4('12');
        $this->assertFalse($subject->checkType('12'));
        $this->assertFalse($subject->checkType(12));
    }

    public function testEmptyTypeAllowsAllTypes(): void
    {
        $testItem1 = new TestItem(1000);
        $testCollection = new GenericCollection();
        $testCollection[] = $testItem1;
        $this->assertCount(1, $testCollection);

        $testCollection[] = random_int(1, 100000);
        $this->assertCount(2, $testCollection);

        $testCollection[] = uniqid('test', true);
        $this->assertCount(3, $testCollection);
    }

    public function testGetClass(): void
    {
        $testCollection = new GenericCollection();
        $this->assertEquals('JTL\Generic\GenericCollection', $testCollection->getClass());
    }

    public function testCanChunk(): void
    {
        $pieces = [];
        $sut = new GenericCollection('string');
        for ($i = 0; $i < 10; $i++) {
            $pieces[$i] = uniqid();
            $sut->add($pieces[$i]);
        }

        self::assertSame(10, $sut->count());
        $chunkList = $sut->chunk(2);
        self::assertCount(5, $chunkList);
        foreach ($chunkList as $key => $chunk) {
            self::assertInstanceOf(GenericCollection::class, $chunk);
            self::assertSame(2, $chunk->count());
            foreach ($chunk as $chunkKey => $piece) {
                self::assertSame($pieces[($key*2)+$chunkKey], $piece);
            }
        }
    }
}
