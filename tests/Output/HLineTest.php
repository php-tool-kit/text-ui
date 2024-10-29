<?php
namespace TextUI\Tests\Output;

use PHPUnit\Framework\TestCase;
use TextUI\Output\HLine;
use TextUI\Utils;

/**
 * Unit tests for TextUI\Output\HLine class.
 *
 * @author everton3x
 */
class HLineTest extends TestCase
{

    public function testDraw(): void
    {
        $terminalColumns = Utils::detectScreenColumns();
        $terminalObject = new HLine();
        $expectedOutput = str_repeat($terminalObject->char, $terminalColumns);
        
        $this->expectsOutput($expectedOutput);
        $terminalObject->draw();
    }
}
