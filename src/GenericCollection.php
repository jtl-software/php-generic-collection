<?php

declare(strict_types=1);

namespace JTL\Generic;

use Closure;
use Traversable;

class GenericCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    protected array $itemList = [];
    protected ?string $type;

    /**
     * Create a new GenericCollection from a variable-length argument list
     * @param mixed ...$items
     * @return static
     */
    public static function from(...$items)
    {
        return (new static())->addItemList($items);
    }

    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     */
    public function add($value): void
    {
        if (!$this->checkType($value)) {
            throw new \InvalidArgumentException('Invalid type');
        }

        $this->itemList[] = $value;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->itemList);
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function checkType($item): bool
    {
        return $this->type === null
            || ($this->type === 'string' && \is_string($item))
            || (($this->type === 'integer' || $this->type === 'int') && \is_int($item))
            || $item instanceof $this->type;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->itemList[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->itemList[$offset] ?? null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->checkType($value)) {
            throw new \InvalidArgumentException('Invalid type');
        }

        $offset !== null ? $this->itemList[$offset] = $value : $this->itemList[] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        unset($this->itemList[$offset]);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return \count($this->itemList);
    }

    /**
     * Add a raw array of items to the collection
     *
     * @param GenericCollection|array $itemList
     * @return GenericCollection
     */
    public function addItemList($itemList): self
    {
        foreach ($itemList as $item) {
            if (!$this->checkType($item)) {
                throw new \InvalidArgumentException('Invalid type');
            }

            $this->itemList[] = $item;
        }

        return $this;
    }

    public function getClass()
    {
        return static::class;
    }

    // ==== Assorted iterator goodies ported from the rust standard library ====

    /**
     * Executes 'func' for every item in the collection with the item as parameter
     *
     * @param Closure $func
     * @return GenericCollection
     */
    public function each(Closure $func): self
    {
        foreach ($this->itemList as $item) {
            $func($item);
        }

        return $this;
    }

    /**
     * Applies 'func' to every item via array_map
     * Important: Make sure to return the input parameter to avoid re-typing the collection
     *
     * @param Closure $func
     * @return GenericCollection
     */
    public function map(Closure $func): self
    {
        $this->itemList = array_map($func, $this->itemList);
        return $this;
    }

    /**
     * Applies 'func' as a filter to every item via array_filter
     *
     * @param Closure $func
     * @return GenericCollection
     */
    public function filter(Closure $func): self
    {
        $this->itemList = array_filter($this->itemList, $func);
        return $this;
    }

    /**
     * Explicitly clones the collection and returns the new collection leaving the original unmodified.
     *
     * @return GenericCollection
     */
    public function clone(): self
    {
        $items = $this->itemList;
        $collection = new static($this->getType());

        foreach ($items as $item) {
            $collection[] = clone $item;
        }

        return $collection;
    }

    /**
     * Chains a second collection of the same type to the end of the current collection.
     *
     * @param GenericCollection $other
     * @return GenericCollection
     */
    public function chain(GenericCollection $other): self
    {
        if ($other->getType() !== $this->getType()) {
            throw new \InvalidArgumentException('Invalid type');
        }

        foreach ($other as $item) {
            if (!$this->checkType($item)) {
                throw new \InvalidArgumentException('Invalid type');
            }

            $this->itemList[] = $item;
        }

        return $this;
    }

    /**
     * Zips up another collection with the current one but returns a new collection leaving the original unmodified.
     * Each item will be a tuple where the first element is an item from the first collection and the second
     * element is an item from the other collection.
     * Each tuple is represented as a Zip class.
     * If the two collections to be zipped are of different lengths the zip will only contain the elements up to
     * the first collection to return null. Remaining items are ignored.
     *
     * @param GenericCollection $other
     * @return ZippedCollection
     */
    public function zip(GenericCollection $other): ZippedCollection
    {
        $newCollection = new ZippedCollection();
        $newCollection->setLeftOriginalClassName(static::class);
        $newCollection->setLeftOriginalItemType($this->getType());
        $newCollection->setRightOriginalClassName($other->getClass());
        $newCollection->setRightOriginalItemType($other->getType());

        $count = \count($this->itemList);

        for ($i = 0; $i < $count; ++$i) {
            if ($this->itemList[$i] === null || $other[$i] === null) {
                return $newCollection;
            }

            if (!$other->checkType($other[$i])) {
                throw new \InvalidArgumentException('Invalid type in other collection. Expected: ' . $other->getType());
            }

            if (!$this->checkType($this->itemList[$i])) {
                throw new \InvalidArgumentException('Invalid type in collection. Expected: ' . $this->getType());
            }

            $newCollection[] = new Zip($this->itemList[$i], $other[$i]);
        }

        return $newCollection;
    }

    /**
     * Creates two new collections based on whether or the predicate returns true or false.
     * 'func' is called with each item and has to return a boolean value.
     *
     * @param Closure $func
     * @return array An array of two collections with index 0 being the 'true' collection and index 1 being the
     * 'false' collection
     */
    public function partition(Closure $func): array
    {
        $trueCollection = new static($this->getType());
        $falseCollection = new static($this->getType());

        foreach ($this->itemList as $item) {
            if ($func($item)) {
                $trueCollection[] = $item;
            } else {
                $falseCollection[] = $item;
            }
        }

        return [$trueCollection, $falseCollection];
    }

    /**
     * Reduces all elements of the collection to a single value using array_reduce.
     * Parameter 1 of 'func' is the carry i.e. the return value of the previous iteration
     * Parameter 2 of 'func' is the current item
     *
     * @param Closure $func
     * @return mixed
     */
    public function reduce(Closure $func)
    {
        return \array_reduce($this->itemList, $func);
    }

    /**
     * Tests if every element of the collection matches a predicate.
     * all() is short-circuiting; in other words, it will stop processing as soon as it finds a false,
     * given that no matter what else happens, the result will also be false.
     * An empty collection returns true.
     *
     * @param Closure $func
     * @return bool
     */
    public function all(Closure $func): bool
    {
        foreach ($this->itemList as $item) {
            if (!$func($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tests if any element of the collection matches a predicate.
     * any() is short-circuiting; in other words, it will stop processing as soon as it finds a true,
     * given that no matter what else happens, the result will also be true.
     * An empty collection returns false.
     *
     * @param Closure $func
     * @return bool
     */
    public function any(Closure $func): bool
    {
        foreach ($this->itemList as $item) {
            if ($func($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Searches for an element of an collection that satisfies a predicate.
     * find() is short-circuiting; in other words, it will stop processing as soon as the closure returns true.
     *
     * @param Closure $func
     * @return mixed|null
     */
    public function find(Closure $func)
    {
        foreach ($this->itemList as $item) {
            if ($func($item)) {
                return $item;
            }
        }

        return null;
    }
}
