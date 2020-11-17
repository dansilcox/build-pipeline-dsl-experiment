<?php

declare(strict_types=1);

namespace JoistTest\Ast\Config;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Config\Parameter;

class ConfigBlockTest extends TestCase
{
    public function testConstruct(): void
    {
        self::assertInstanceOf(ConfigBlock::class, new ConfigBlock());
    }

    public function testSetGetParamsAst(): void
    {
        $objectUnderTest = new ConfigBlock();

        self::assertEmpty($objectUnderTest->getParameters());

        $parameterAst = new Parameter('myString', 'string');
        $parameterAst2 = new Parameter('myNumber', 'number');
        $parameterAst3 = new Parameter('myEnum', 'enum', ['a', 'b', 'c', 1, 2, 3]);

        $expected = [
            'myString:string' => $parameterAst,
            'myNumber:number' => $parameterAst2,
            'myEnum:enum'     => $parameterAst3
        ];
        foreach ($expected as $param) {
            $objectUnderTest->addParameterAst($param);
        }
        self::assertSame($expected, $objectUnderTest->getParameters());
    }

    public function testSetGetParamsManually(): void
    {
        $objectUnderTest = new ConfigBlock();

        self::assertEmpty($objectUnderTest->getParameters());

        $identifier1 = 'myString';
        $identifierType1 = 'string';
        $identifier2 = 'myNumber';
        $identifierType2 = 'number';
        $identifier3 = 'myEnum';
        $identifierType3 = 'enum';
        $allowedValues = ['a', 'b', 'c', 1, 2, 3];

        $parameterAst = new Parameter($identifier1, $identifierType1);
        $parameterAst2 = new Parameter($identifier2, $identifierType2);
        $parameterAst3 = new Parameter($identifier3, $identifierType3, $allowedValues);

        $objectUnderTest->addParameter($identifier1, $identifierType1);
        $objectUnderTest->addParameter($identifier2, $identifierType2);
        $objectUnderTest->addParameter($identifier3, $identifierType3, $allowedValues);

        $expected = [
            'myString:string' => $parameterAst,
            'myNumber:number' => $parameterAst2,
            'myEnum:enum'     => $parameterAst3
        ];

        self::assertEquals($expected, $objectUnderTest->getParameters());
    }
}
