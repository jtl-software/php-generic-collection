<?php

declare(strict_types=1);

namespace JTL\Generic;

class ZippedCollection extends GenericCollection
{
    private string $leftOriginalClassName;

    private string $leftOriginalItemType;

    private string $rightOriginalClassName;

    private string $rightOriginalItemType;

    public function __construct()
    {
        parent::__construct(Zip::class);
    }

    /**
     * @return string
     */
    public function getLeftOriginalClassName(): string
    {
        return $this->leftOriginalClassName;
    }

    /**
     * @param string $leftOriginalClassName
     * @return ZippedCollection
     */
    public function setLeftOriginalClassName(string $leftOriginalClassName): ZippedCollection
    {
        $this->leftOriginalClassName = $leftOriginalClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLeftOriginalItemType(): string
    {
        return $this->leftOriginalItemType;
    }

    /**
     * @param string $leftOriginalItemType
     * @return ZippedCollection
     */
    public function setLeftOriginalItemType(string $leftOriginalItemType): ZippedCollection
    {
        $this->leftOriginalItemType = $leftOriginalItemType;
        return $this;
    }

    /**
     * @return string
     */
    public function getRightOriginalClassName(): string
    {
        return $this->rightOriginalClassName;
    }

    /**
     * @param string $rightOriginalClassName
     * @return ZippedCollection
     */
    public function setRightOriginalClassName(string $rightOriginalClassName): ZippedCollection
    {
        $this->rightOriginalClassName = $rightOriginalClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRightOriginalItemType(): string
    {
        return $this->rightOriginalItemType;
    }

    /**
     * @param string $rightOriginalItemType
     * @return ZippedCollection
     */
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
     */
    public function unzip(): array
    {
        $leftOriginalClassName = $this->getLeftOriginalClassName();
        $rightOriginalClassName = $this->getRightOriginalClassName();
        $leftCollection = new $leftOriginalClassName($this->getLeftOriginalItemType());
        $rightCollection = new $rightOriginalClassName($this->getRightOriginalItemType());

        /** @var Zip $item */
        foreach ($this->itemList as $item) {
            if (!$leftCollection->checkType($item->getLeft())
                || !$rightCollection->checkType($item->getRight())) {
                throw new \InvalidArgumentException('Invalid type');
            }

            $leftCollection[] = $item->getLeft();
            $rightCollection[] = $item->getRight();
        }

        return [$leftCollection, $rightCollection];
    }
}
