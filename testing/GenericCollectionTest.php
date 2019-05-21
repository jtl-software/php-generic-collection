<?php
/**
 * User: mbrandt
 * Date: 15/03/19
 */

namespace JTL\Generic;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class GenericCollectionTest
 *
 * @covers \JTL\Generic\GenericCollection
 *
 * @uses \JTL\Generic\Zip
 * @uses \JTL\Generic\ZippedCollection
 */
class GenericCollectionTest extends TestCase
{
    public function testCanCreateCollectionWithType()
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertEquals(123, $collection[0]->a);
    }

    public function testCanCreateCollectionWithTypeFromArgumentList()
    {
        $testItem1 = new TestItem(123);
        $testItem2 = new TestItem(456);
        $collection = TestCollection::from($testItem1, $testItem2);

        $this->assertEquals(123, $collection[0]->a);
        $this->assertEquals(456, $collection[1]->a);
    }

    public function testFailIfSetWithWrongType()
    {
        $collection = new TestCollection();
        $this->expectException(InvalidArgumentException::class);
        $collection[] = random_int(1, 100000);
    }

    public function testCanIterate()
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        foreach ($collection as $item) {
            $this->assertEquals(123, $item->a);
        }
    }

    public function testCanCheckIfOffsetExists()
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertTrue(isset($collection[0]));
    }

    public function testCanUnset()
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        unset($collection[0]);
        $this->assertFalse(isset($collection[0]));
    }

    public function testCanGetType()
    {
        $collection = new TestCollection();

        $this->assertEquals($collection->getType(), TestItem::class);
    }

    public function testCanCountElements()
    {
        $testItem1 = new TestItem(123);
        $collection = new TestCollection();
        $collection[] = $testItem1;

        $this->assertEquals(1, $collection->count());
    }

    public function testCanAddMultipleFromArray()
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

    public function testFailIfAddMultipleFromArrayHasWrongType()
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItemArray = [$testItem1, $testItem2, 'TEST'];
        $collection = new TestCollection();
        $this->expectException(InvalidArgumentException::class);
        $collection->addItemList($testItemArray);
    }

    public function testCanDoEach()
    {
        $testItem1 = new TestItem(111);
        $testItem2 = new TestItem(222);
        $testItem3 = new TestItem(333);
        $testItemArray = [$testItem1, $testItem2, $testItem3];
        $collection = new TestCollection();
        $collection->addItemList($testItemArray);

        $each = function ($item) {
            $this->assertInstanceOf(TestItem::class, $item);
        };

        $collection->each($each);
    }

    public function testCanMap()
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

    public function testCanFilter()
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

    public function testCanClone()
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

        $this->assertEquals(2, $filteredCollection->count());
        $this->assertEquals(3, $collection->count());
    }

    public function testCanChain()
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

    public function testFailChainIfTypesIncompatible()
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

        $this->expectException(InvalidArgumentException::class);
        $collection->chain($collection2);
    }

    public function testFailChainIfItemTypeIncompatible()
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

    public function testCanZip()
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
    }

    public function testCanZipWithDifferentLengths()
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

    public function testFailZipIfOtherCollectionTypesIncompatible()
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
        $collection->zip($collection2);
    }

    public function testFailZipIfOwnCollectionTypesIncompatible()
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

        $this->expectException(InvalidArgumentException::class);
        $collection->zip($collection2);
    }

    public function testCanPartition()
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

    public function testCanReduce()
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

    public function testCanTestAll()
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

    public function testCanTestAny()
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

    public function testCanFind()
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

    public function testCanOmitTypeInConstructor()
    {
        $testItem1 = new TestItem(1000);
        $testCollection = new TestCollection3();
        $testCollection[] = $testItem1;
        $this->assertCount(1, $testCollection);
    }

    public function testEmptyTypeAllowsAllTypes()
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
}
