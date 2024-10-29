<?php
namespace TextUI\Tests\Input;

use PHPUnit\Framework\TestCase;
use TextUI\Input\DateEntry;
use TextUI\Tests\Mocks\IOMock;

/**
 * Unit tests for TextUI\Input\DateEntry class.
 *
 * @author everton3x
 */
class DateEntryTest extends TestCase
{

    public function testRead(): void
    {
        $dateFormat = 'Y-m-d';
        $inputMock = date($dateFormat);
        IOMock::$input = $inputMock;
        $terminalObject = new DateEntry('This is as PHPUnit test!', $dateFormat);
        $result = $terminalObject->read();

        $this->assertEquals($inputMock, $result->format($dateFormat));
    }
}
