<?php

declare(strict_types=1);

/**
 * User: mbrandt
 * Date: 15/03/19
 */

namespace JTL\Generic;

class Zip
{
    private $left;
    private $right;

    public function __construct($first, $second)
    {
        $this->left = $first;
        $this->right = $second;
    }

    /**
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }
}
