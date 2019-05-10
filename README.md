# Generic Collection

[![Build status](https://travis-ci.com/jtl-software/php-generic-collection.svg?branch=master)](https://travis-ci.com/jtl-software/php-generic-collection)  

An implementation of generic collections in PHP using iterators.
Brings type safety to your arrays without hacks or ugly code.

## Usage  

The primary way of using this library is to create your own collection 
class which extends the `GenericCollection` and sets its type in the
constructor like so:  

```php
use JTL\Generic\GenericCollection;

class MyItemCollection extends GenericCollection {
    public function __construct()
    {
        parent::__construct(MyItem::class);
    }
}
```  

You can now add new items to your collection either in bulk or using
`$collection[] = ...` syntax.  

```php
$collection = new MyItemCollection();
$item1 = new MyItem(1);
$item2 = new MyItem(2);
$item3 = new MyItem(3);

$collection[] = $item1;
$collection->addItemList([$item2, $item3]);
```  

Trying to add a new item that's not of the specified type will throw 
an InvalidArgumentException.  

```php
$collection = new MyItemCollection();
$item1 = new MyItem(1);
$item2 = 'not MyItem';
$item3 = new MyItem(3);

$collection[] = $item1;
$collection[] = $item2; // <- Doesn't work. This will throw an exception
$collection->addItemList([$item2, $item3]); // <- This won't work either because $item2 is not a 'MyItem'
```  

## Advanced type checking

The default type checking function only checks if the item to be added is an instance of
the given type or, if you set the type to 'string', if it is a string.  
If you desire more advanced type checking you can implement your own algorithm easily.  
Your custom collection class simply needs to override the `checkType` method and return
*true* if the type matches or *false* if it doesn't.  

Here's an example of a collection that only accepts even numbers. 

```php
use JTL\Generic\GenericCollection;

class EvenCollection extends GenericCollection {
    public function checkType($item): bool {
        return $item % 2 === 0;
    }
}

$e = new EvenCollection();
$e[] = 2;
$e[] = 4;
$e[] = 5; // <- Doesn't work. This will throw an exception
```

## Iterator methods  

In addition to ensuring type safety the GenericCollection also implements a few
methods to make working with iterators more pleasant.  
**Note:** Some of these methods mutate the state of the collection. 
If this behavior is not desired you may wish to `clone` the collection before
modifying it.  

The following methods are currently implemented:  

| Method                   | Explanation                                                                                                 |
|:-------------------------|:------------------------------------------------------------------------------------------------------------|
| each(Closure)            | Performs a closure on each item in the collection                                                           |
| map(Closure)             | Replaces every element in the collection with the result of the closure                                     |
| filter(Closure)          | Modifies the collection to leave only items in the collection for which the closure returns true            |
| clone()                  | Clones the collection                                                                                       |
| chain(GenericCollection) | Appends another collection to the end of the current collection                                             |
| zip(GenericCollection)   | Zips another collection with the current collection                                                         |
| partition(Closure)       | Creates two collections, one for which the closure returns true and one for which the closure returns false |
| reduce(Closure)          | Reduces the collection to a final value based on a closure                                                  |
| all(Closure)             | Returns true if the closure returns true **for each element**, otherwise false                              |
| any(Closure)             | Returns true if the closure returns true **for at least one element**, otherwise false                      |
| find(Closure)            | Returns **the first element** for which the closure returns true                                            |

### What does zipping do?

Zipping joins two collections together so that each element of the new collection is a 
tuple of one element from the first, or left, collection and one element from the second,
or right, collection.
To keep the code readable you access the elements of the tuple using `getLeft()` and `getRight()`.

Example:  

```php
$itemList1 = new ItemCollection();
$itemList2 = new ItemCollection();
 
$item11 = new Item('123');
$item12 = new Item('789');
 
$item21 = new Item('456');
$item22 = new Item('0');
 
$itemList1[] = $item11;
$itemList1[] = $item12;
 
$itemList2[] = $item21;
$itemList2[] = $item21;
 
$zip = $itemList1->zip($itemList2);
 
foreach ($zip as $tuple) {
    echo $tuple->getLeft()->getContent() . $tuple->getRight()->getContent() . "\n";
}
 
// Output:
123456
7890
```  

**Important!** You can zip two collections of different types!  

### Unzipping

A zipped collection can be unzipped into the two original collections using the `unzip()`
method on a zipped collection.  

Building upon the previous example:  

```php
[$unzippedItemList1, $unzippedItemList2] = $zip->unzip();
```

At this point `$itemList1` and `$unzippedItemList1` will be identical. The same is true
for `$itemList2` and `$unzippedItemList2`.

The zipped collection stores the original collection types internally so
unzipping is even possible if you zip two different collection types.

# License

The code is released under the [MIT License](https://github.com/jtl-software/php-generic-collection/blob/master/LICENSE).