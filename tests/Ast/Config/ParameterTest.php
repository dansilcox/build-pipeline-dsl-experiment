<?php

declare(strict_types=1);

namespace JoistTest\Ast\Config;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Config\Parameter;

class ParameterTest extends TestCase
{
    public function testConstructAndGetters(): void
    {
        $identifierName = 'thing';
        $identifierType = 'enum';
        $allowedValues = [
            'a',
            'c',
            'e'
        ];

        $objectUnderTest = new Parameter(
            $identifierName,
            $identifierType,
            $allowedValues
        );
        self::assertInstanceOf(Parameter::class, $objectUnderTest);

        self::assertSame($identifierName, $objectUnderTest->getName());
        self::assertSame($identifierType, $objectUnderTest->getType());
        self::assertSame($allowedValues, $objectUnderTest->getAllowedValues());
    }
}
