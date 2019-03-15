<?php
/**
 * User: mbrandt
 * Date: 15/03/19
 */

use JTL\Generic\Zip;
use PHPUnit\Framework\TestCase;

/**
 * Class ZippedCollectionTest
 *
 * @covers \JTL\Generic\ZippedCollection
 *
 * @uses \JTL\Generic\Zip
 * @uses \JTL\Generic\GenericCollection
 */
class ZippedCollectionTest extends TestCase
{
    public function testCanUnzip()
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

        $this->assertEquals(111, $zip[0]->getLeft()->a);
        $this->assertEquals(1000, $zip[0]->getRight()->a);

        $this->assertEquals(222, $zip[1]->getLeft()->a);
        $this->assertEquals(2000, $zip[1]->getRight()->a);

        $this->assertEquals(333, $zip[2]->getLeft()->a);
        $this->assertEquals(3000, $zip[2]->getRight()->a);

        [$unzipCollection1, $unzipCollection2] = $zip->unzip();

        $this->assertEquals($collection, $unzipCollection1);
        $this->assertEquals($collection2, $unzipCollection2);
    }

    public function testFailIfZipIsInvalid()
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

        $zip[0] = new Zip(1, 2);

        $this->expectException(InvalidArgumentException::class);
        $zip->unzip();
    }
}
