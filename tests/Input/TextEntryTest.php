<?php
namespace TextUI\Tests\Input;

use PHPUnit\Framework\TestCase;
use TextUI\Input\TextEntry;
use TextUI\Tests\Mocks\IOMock;

/**
 * Unit tests for TextUI\Input\TextEntry class.
 *
 * @author everton3x
 */
class TextEntryTest extends TestCase
{

    public function testRead(): void
    {
        $inputMock = 'Hello TextUI + PHPUnit';
        IOMock::$input = $inputMock;
        $terminalObject = new TextEntry('This is as PHPUnit test!');
        $result = $terminalObject->read();

        $this->assertEquals($inputMock, $result);
    }
}
