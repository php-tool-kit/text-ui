<?php
namespace TextUI\Tests\Input;

use PHPUnit\Framework\TestCase;
use TextUI\Input\TextMultiLineEntry;
use TextUI\Tests\Mocks\IOMock;

/**
 * Unit tests for TextUI\Input\TextMultiLineEntry class.
 *
 * @author everton3x
 */
class TextMultiLineEntryTest extends TestCase
{
    public function testRead(): void
    {
        $inputMock = ['Line 1', 'Line 2'];
        IOMock::$options = $inputMock;
        $terminalObject = new TextMultiLineEntry('This is as PHPUnit test!');
        $result = $terminalObject->read();
        $this->assertEquals($inputMock, $result);
    }
}
