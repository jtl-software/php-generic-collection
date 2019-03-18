<?php
/**
 * User: mbrandt
 * Date: 18/03/19
 */

namespace JTL\Generic;

class TestItem
{
    public $a;

    public function __construct($a)
    {
        $this->a = $a;
    }
}

class TestItem2
{
    public $a;

    public function __construct($a)
    {
        $this->a = $a;
    }
}

class TestCollection extends GenericCollection
{
    public function __construct()
    {
        parent::__construct(TestItem::class);
    }
}

class TestCollection2 extends GenericCollection
{
    public function __construct()
    {
        parent::__construct(TestItem2::class);
    }
}

class TestCollection3 extends GenericCollection
{
    public function checkType($item): bool
    {
        return true;
    }
}