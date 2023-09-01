<?php

declare(strict_types=1);

namespace JTL\Generic;

/**
 * @extends GenericCollection<string>
 */
final class StringCollection extends GenericCollection
{
    public function __construct()
    {
        parent::__construct('string');
    }
}
