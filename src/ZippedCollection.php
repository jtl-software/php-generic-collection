<?php

declare(strict_types=1);

namespace JTL\Generic;

use InvalidArgumentException;

/**
 * @template TCollectionItemLeft
 * @template TCollectionItemRight
 * @extends GenericCollection<Zip<TCollectionItemLeft,TCollectionItemRight>>
 */
class ZippedCollection extends GenericCollection
{
    /**
     * @var class-string<GenericCollection>|''
     */
    private string $leftOriginalClassName = '';
    private string $leftOriginalItemType = '';
    /**
     * @var class-string<GenericCollection>|''
     */
    private string $rightOriginalClassName = '';
    private string $rightOriginalItemType = '';

    public function __construct()
    {
        parent::__construct(Zip::class);
    }

    /**
     * @return class-string<GenericCollection>|''
     */
    public function getLeftOriginalClassName(): string
    {
        return $this->leftOriginalClassName;
    }

    /**
     * @param class-string<GenericCollection> $leftOriginalClassName
     */
    public function setLeftOriginalClassName(string $leftOriginalClassName): ZippedCollection
    {
        $this->leftOriginalClassName = $leftOriginalClassName;
        return $this;
    }

    public function getLeftOriginalItemType(): string
    {
        return $this->leftOriginalItemType;
    }

    public function setLeftOriginalItemType(string $leftOriginalItemType): ZippedCollection
    {
        $this->leftOriginalItemType = $leftOriginalItemType;
        return $this;
    }

    /**
     * @return class-string<GenericCollection>|''
     */
    public function getRightOriginalClassName(): string
    {
        return $this->rightOriginalClassName;
    }

    /**
     * @param class-string<GenericCollection> $rightOriginalClassName
     */
    public function setRightOriginalClassName(string $rightOriginalClassName): ZippedCollection
    {
        $this->rightOriginalClassName = $rightOriginalClassName;
        return $this;
    }

    public function getRightOriginalItemType(): string
    {
        return $this->rightOriginalItemType;
    }

    public function setRightOriginalItemType(string $rightOriginalItemType): ZippedCollection
    {
        $this->rightOriginalItemType = $rightOriginalItemType;
        return $this;
    }

    /**
     * Converts a collection of Zips into two collection of their original types.
     * The left element is put into the left collection and the right element into the right collection.
     *
     * @return array An array of two collections with index 0 being the collection of left elements and index 1
     * being the collection of right elements
     *
     * @throws InvalidArgumentException
     */
    public function unzip(): array
    {
        $leftOriginalClassName = $this->getLeftOriginalClassName();
        $rightOriginalClassName = $this->getRightOriginalClassName();
        /** @var GenericCollection $leftCollection */
        $leftCollection = new $leftOriginalClassName($this->getLeftOriginalItemType());
        /** @var GenericCollection $rightCollection */
        $rightCollection = new $rightOriginalClassName($this->getRightOriginalItemType());

        /** @var Zip $item */
        foreach ($this->itemList as $item) {
            if (!$leftCollection->checkType($item->getLeft())
                || !$rightCollection->checkType($item->getRight())) {
                throw new InvalidArgumentException('Invalid type left or right');
            }

            $leftCollection[] = $item->getLeft();
            $rightCollection[] = $item->getRight();
        }

        return [$leftCollection, $rightCollection];
    }
}
