<?php declare(strict_types=1);
/**
 * User: sleipi
 * Date: 07/01/20
 */

namespace JTL\Generic;

/**
 * @method string offsetGet($offset)
 */
final class StringCollection extends GenericCollection
{
    public function __construct()
    {
        parent::__construct('string');
    }
}
