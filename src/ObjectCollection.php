<?php

declare(strict_types=1);

namespace JTL\Generic;

use InvalidArgumentException;

/**
 * @template T as object
 * @extends GenericCollection<T>
 */
final class ObjectCollection extends GenericCollection
{
    /**
     * @param null|class-string<T> $className
     */
    public function __construct(string $className = null)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException('Invalid argument, please use a class-string here.');
        }

        parent::__construct($className);
    }

    /**
     * Create a new ObjectCollection from a variable-length argument list.
     *
     * - The type of the collection will be defined automatically by the first item.
     *
     * @template TFrom as T
     * @param TFrom ...$items
     * @return static<TFrom>
     * @phpstan-ignore-next-line | static<T> is not 100% supported
     */
    public static function from(...$items): static
    {
        /* https://github.com/phpstan/phpstan/issues/5512 */
        /* @phpstan-ignore-next-line | static<T> is not 100% supported, but it works */
        return (new static($items[0]::class))->addItemList($items);
    }

    /**
     * @return null|T
     */
    public function offsetGet($offset): mixed
    {
        return $this->itemList[$offset] ?? null;
    }
}
