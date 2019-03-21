<?php declare(strict_types = 1);

namespace TimurFlush\Math;

class Exception extends \Exception
{
    public static function invalidNumber()
    {
        return new self('Passed invalid number.');
    }
}
