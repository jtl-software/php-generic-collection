<?php

declare(strict_types=1);

namespace JTL\Generic;

/**
 * @template T
 */
class TestItem
{
    /**
     * @var T
     */
    public $a;

    /**
     * @param T $a
     */
    public function __construct($a)
    {
        $this->a = $a;
    }
}

/**
 * @template T
 */
class TestItem2
{
    /**
     * @var T
     */
    public $a;

    /**
     * @param T $a
     */
    public function __construct($a)
    {
        $this->a = $a;
    }
}

/**
 * @extends GenericCollection<TestItem>
 */
class TestCollection extends GenericCollection
{
    public function __construct()
    {
        parent::__construct(TestItem::class);
    }
}

/**
 * @extends GenericCollection<TestItem2>
 */
class TestCollection2 extends GenericCollection
{
    public function __construct()
    {
        parent::__construct(TestItem2::class);
    }
}

/**
 * @extends GenericCollection<mixed>
 */
class TestCollection3 extends GenericCollection
{
    public function checkType($item): bool
    {
        return true;
    }
}

/**
 * @extends GenericCollection<mixed>
 */
class TestCollection4 extends GenericCollection
{
    public function __construct(string $type = null)
    {
        parent::__construct($type);
    }
}
