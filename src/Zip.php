<?php
/**
 * User: mbrandt
 * Date: 15/03/19
 */

namespace JTL\Generic;

class Zip
{
    public function __construct(private mixed $first, private mixed $second)
    {
    }

    public function getLeft(): mixed
    {
        return $this->first;
    }

    public function getRight(): mixed
    {
        return $this->second;
    }
}
