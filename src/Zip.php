<?php

declare(strict_types=1);

namespace JTL\Generic;

/**
 * @template TCollectionItemLeft
 * @template TCollectionItemRight
 */
class Zip
{
    /**
     * @var TCollectionItemLeft
     */
    private $left;
    /**
     * @var TCollectionItemRight
     */
    private $right;

    /**
     * @param TCollectionItemLeft $first
     * @param TCollectionItemRight $second
     */
    public function __construct($first, $second)
    {
        $this->left = $first;
        $this->right = $second;
    }

    /**
     * @return TCollectionItemLeft
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return TCollectionItemRight
     */
    public function getRight()
    {
        return $this->right;
    }
}
