<?php

declare(strict_types=1);

namespace JTL\Generic;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 * @implements IteratorAggregate<int,T>
 * @implements ArrayAccess<int,T>
 */
class GenericCollection implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @var array<int,T>
     */
    protected array $itemList = [];
    protected ?string $type;

    /**
     * Create a new GenericCollection from a variable-length argument list.
     *
     * @param T ...$items
     */
    public static function from(...$items): static
    {
        return (new static())->addItemList($items);
    }

    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * @param T $value
     */
    public function add($value)
    {
        if (!$this->checkType($value)) {
            throw new InvalidArgumentException('Invalid type to add');
        }

        $this->itemList[] = $value;
    }

    /**
     * Retrieve an external iterator.
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ArrayIterator<int,T> An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator(): Iterator|Traversable
    {
        return new ArrayIterator($this->itemList);
    }

    /**
     * @param T $item
     */
    public function checkType($item): bool
    {
        return $this->type === null
            || ($this->type === 'string' && \is_string($item))
            || (($this->type === 'integer' || $this->type === 'int') && \is_int($item))
            || $item instanceof $this->type;
    }

    /**
     * Whether an offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param int $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * </p>
     */
    public function offsetExists($offset): bool
    {
        return isset($this->itemList[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $offset <p>
     * The offset to retrieve.
     * </p>
     * @return T Can return all value types.
     */
    public function offsetGet($offset): mixed
    {
        return $this->itemList[$offset] ?? null;
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param null|int $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param T $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->checkType($value)) {
            throw new InvalidArgumentException('Invalid type for offset');
        }

        $offset !== null ? $this->itemList[$offset] = $value : $this->itemList[] = $value;
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->itemList[$offset]);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Count elements of an object.
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * <p>
     * The return value is cast to an integer.
     * </p>
     */
    public function count(): int
    {
        return count($this->itemList);
    }

    /**
     * Add a raw array of items to the collection.
     *
     * @param Traversable<int,T>|array<int,T> $itemList
     */
    public function addItemList($itemList): static
    {
        foreach ($itemList as $item) {
            if (!$this->checkType($item)) {
                throw new InvalidArgumentException('Invalid type to add in list');
            }

            $this->itemList[] = $item;
        }

        return $this;
    }

    /**
     * @return class-string<static>
     */
    public function getClass(): string
    {
        return static::class;
    }

    // ==== Assorted iterator goodies ported from the rust standard library ====

    /**
     * Executes 'func' for every item in the collection with the item as parameter.
     *
     * @param Closure(T):void  $func
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
     * Applies 'func' to every item via array_map.
     *
     * Important: Make sure to return the input parameter to avoid re-typing the collection
     *
     * @param Closure(T):mixed $func
     * @return static<mixed>
     */
    public function map(Closure $func): self
    {
        $this->itemList = array_map($func, $this->itemList);
        return $this;
    }

    /**
     * Applies 'func' as a filter to every item via array_filter.
     *
     * @param Closure(T):bool $func
     * @return static<T>
     */
    public function filter(Closure $func)
    {
        $this->itemList = array_filter($this->itemList, $func);
        return $this;
    }

    /**
     * Explicitly clones the collection and returns the new collection leaving the original unmodified.
     *
     * @return static<T>
     */
    public function clone()
    {
        $collection = new static($this->getType());

        foreach ($this->itemList as $item) {
            $collection[] = clone $item;
        }

        return $collection;
    }

    /**
     * Chains a second collection of the same type to the end of the current collection.
     *
     * @param GenericCollection<T> $other
     * @return GenericCollection<T>
     */
    public function chain(GenericCollection $other): self
    {
        if ($other->getType() !== $this->getType()) {
            throw new InvalidArgumentException('Invalid type by collections');
        }

        foreach ($other as $item) {
            if (!$this->checkType($item)) {
                throw new InvalidArgumentException('Invalid type in collection to add item');
            }

            $this->itemList[] = $item;
        }

        return $this;
    }

    /**
     * Zips up another collection with the current one but returns a new collection leaving the original unmodified.
     *
     * <ul>
     * <li> Each item will be a tuple where the first element is an item from the first collection and the second
     * element is an item from the other collection.</li>
     * <li> Each tuple is represented as a Zip class.</li>
     * <li> If the two collections to be zipped are of different lengths the zip will only contain the elements up to
     * the first collection to return null. Remaining items are ignored.</li>
     * </ul>
     *
     * @template TZip
     * @param GenericCollection<TZip> $other
     * @return ZippedCollection<Zip<T,TZip>>
     */
    public function zip(GenericCollection $other): ZippedCollection
    {
        $newCollection = new ZippedCollection();
        $newCollection->setLeftOriginalClassName(static::class);
        $newCollection->setLeftOriginalItemType($this->getType());
        $newCollection->setRightOriginalClassName($other->getClass());
        $newCollection->setRightOriginalItemType($other->getType());

        $count = count($this->itemList);

        for ($i = 0; $i < $count; ++$i) {
            if ($this->itemList[$i] === null || $other[$i] === null) {
                return $newCollection;
            }

            if (!$other->checkType($other[$i])) {
                throw new InvalidArgumentException('Invalid type in other collection. Expected: ' . $other->getType());
            }

            if (!$this->checkType($this->itemList[$i])) {
                throw new InvalidArgumentException('Invalid type in collection. Expected: ' . $this->getType());
            }

            $newCollection[] = new Zip($this->itemList[$i], $other[$i]);
        }

        return $newCollection;
    }

    /**
     * Creates two new collections based on whether or the predicate returns true or false.
     *
     * - 'func' is called with each item and has to return a boolean value.
     *
     * @param Closure(T):bool  $func
     * @return array{0: static<T>, 1: static<T>} An array of two collections with index 0 being the 'true' collection and index 1 being the
     * 'false' collection
     */
    public function partition(Closure $func)
    {
        /**
         * @var static<T> $trueCollection
         */
        $trueCollection = new static($this->getType());
        /**
         * @var static<T> $falseCollection
         */
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
     *
     * - Parameter 1 of 'func' is the carry i.e. the return value of the previous iteration
     * - Parameter 2 of 'func' is the current item
     *
     * @param Closure(mixed,T):mixed $func
     * @return mixed
     */
    public function reduce(Closure $func)
    {
        return array_reduce($this->itemList, $func);
    }

    /**
     * Tests if every element of the collection matches a predicate.
     *
     * all() is short-circuiting; in other words, it will stop processing as soon as it finds a false,
     * given that no matter what else happens, the result will also be false.
     * An empty collection returns true.
     *
     * @param Closure(T):bool $func
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
     *
     * any() is short-circuiting; in other words, it will stop processing as soon as it finds a true,
     * given that no matter what else happens, the result will also be true.
     * An empty collection returns false.
     *
     * @param Closure(T):bool $func
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
     * Searches for an element of a collection that satisfies a predicate.
     *
     * find() is short-circuiting; in other words, it will stop processing as soon as the closure returns true.
     *
     * @param Closure(T):bool $func
     * @return T|null
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

    /**
     * Split a Collection into chunks.
     *
     * @return list<static<T>>
     */
    public function chunk(int $length, bool $preserveKeys = false): array
    {
        $chunkList = [];
        foreach (array_chunk($this->itemList, $length, $preserveKeys) as $chunk) {
            $chunkList[] = self::from(...$chunk);
        }
        return $chunkList;
    }
}
