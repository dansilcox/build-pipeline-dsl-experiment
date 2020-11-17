<?php

declare(strict_types=1);

namespace JoistTest\Ast;

use Joist\Ast\Stage\Stage as StageAst;
use Joist\Ast\Stage\Step as StepAst;
use Joist\Ast\Stage\Conditional\Always as AlwaysAst;
use PHPUnit\Framework\TestCase;

class StageTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $name = 'My Stage 365';
        $conditional = new AlwaysAst();

        $objectUnderTest = new StageAst($name, $conditional);

        self::assertSame($name, $objectUnderTest->getName());
        self::assertSame($conditional, $objectUnderTest->getConditional());
        self::assertEmpty($objectUnderTest->getSteps());
        $steps = [
            new StepAst()
        ];
        foreach ($steps as $step) {
            $objectUnderTest->addStepAst($step);
        }
        self::assertSame($steps, $objectUnderTest->getSteps());
    }
}
