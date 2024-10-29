<?php
namespace TextUI\Tests\Input;

use PHPUnit\Framework\TestCase;
use TextUI\Input\NumberEntry;
use TextUI\Tests\Mocks\IOMock;

/**
 * Unit tests for TextUI\Input\NumberEntry class.
 *
 * @author everton3x
 */
class NumberEntryTest extends TestCase
{

    public function testRead(): void
    {
        $inputMock = 123456.789;
        IOMock::$input = (string) $inputMock;
        $terminalObject = new NumberEntry('This is as PHPUnit test!');
        $result = $terminalObject->read();

        $this->assertEquals($inputMock, $result);
    }
}
