<?php

/**
 * @see       https://github.com/laminas/laminas-diactoros for the canonical source repository
 * @copyright https://github.com/laminas/laminas-diactoros/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use function array_key_exists;
use function Laminas\Diactoros\marshalHeadersFromSapi as laminas_marshalHeadersFromSapi;
use function strpos;
use function strtolower;
use function strtr;
use function substr;

/**
 * @deprecated Use Laminas\Diactoros\marshalHeadersFromSapi instead
 */
function marshalHeadersFromSapi(array $server)
{
    laminas_marshalHeadersFromSapi(...func_get_args());
}
