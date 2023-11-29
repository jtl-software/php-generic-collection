<?php

declare(strict_types=1);

namespace JTL\Generic;

/**
 * @extends GenericCollection<int>
 */
final class IntegerCollection extends GenericCollection
{
    public function __construct()
    {
        parent::__construct('int');
    }
}
