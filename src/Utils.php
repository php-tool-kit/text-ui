<?php

namespace TextUI;

use DateException;
use TextUI\Enum\OperatingSystem;
use UnexpectedValueException;
use TextUI\Exception\UnknowOSException;

/**
 * Utils for TextUI.
 *
 * @author everton3x
 */
class Utils
{
    /**
     * Detects if the script is running in CLI mode.
     *
     * @return bool TRUE = CLI mode.
     */
    public static function isRunningInCliMode(): bool
    {
        switch (PHP_SAPI) {
            case 'cli':
            case 'cli-server':
                return true;
            default:
                return false;
        }
    }

    /**
     * Detects what the operating system is.
     *
     * @return OperatingSystem
     * @throws UnknowOSException
     */
    public static function detectOS(): OperatingSystem
    {
        switch (PHP_OS_FAMILY) {
            case 'Linux':
                return OperatingSystem::Linux;
            case 'Windows':
                return OperatingSystem::Windows;
            default:
                throw new UnknowOSException();
        }
    }

    /**
     * Detects the number of columns in the terminal.
     *
     * @return int
     * @throws UnknowOSException
     */
    public static function detectScreenColumns(): int
    {
        switch (self::detectOS()) {
            case OperatingSystem::Windows:
                exec('mode con', $output, $exitCode);
                $outputColumns = preg_grep("/(col)/i", $output);
                if (!is_array($outputColumns)) {
                    throw new UnexpectedValueException();
                }
                if (count($outputColumns) === 1) {
                    return (int) join('', preg_replace("/[^0-9]/", "", $outputColumns));
                }
                return 0;
            case OperatingSystem::Linux:
                exec('tput cols', $output, $exitCode);
                return (int) join('', $output);
            default:
                throw new UnknowOSException();
        }
    }

    /**
     * Detects the number of lines in the terminal.
     *
     * @return int
     * @throws UnknowOSException
     */
    public static function detectScreenLines(): int
    {
        switch (self::detectOS()) {
            case OperatingSystem::Windows:
                exec('mode con', $output, $exitCode);
                $outputLines = preg_grep("/(lin)/i", $output);
                if (!is_array($outputLines)) {
                    throw new UnexpectedValueException();
                }
                if (count($outputLines) === 1) {
                    return (int) join('', preg_replace("/[^0-9]/", "", $outputLines));
                }
                return 0;
            case OperatingSystem::Linux:
                exec('tput lines', $output, $exitCode);
                return (int) join('', $output);
            default:
                throw new UnknowOSException();
        }
    }

    /**
     * Calculates the number of years, months, days, hours, minutes and seconds between two timestamps.
     *
     * @param int $timestamp1
     * @param int $timestamp2
     * @return array<int> Array with keys years, months, days, hours, minutes and seconds.
     */
    public static function getDiffBetweenTimes(int $timestamp1, int $timestamp2): array
    {
        $data1 = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp1));
        $data2 = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp2));
        if ($data1 === false) {
            throw new DateException("Timestamp $timestamp1 is invalid.");
        }
        if ($data2 === false) {
            throw new DateException("Timestamp $timestamp2 is invalid.");
        }
        $diff = $data1->diff($data2);
        return [
            'years' => $diff->y,
            'month' => $diff->m,
            'days' => $diff->d,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
        ];
    }

    /**
     * Breaks a string into an array where each element has the length defined
     * by $len.
     *
     * First, the string is split by the `PHP_EOL` line breaks, only after
     * that, the lines longer than $len are also broken.
     *
     * @param string $text
     * @param int<1, max> $len
     * @return array<string>
     */
    public static function strToParagraph(string $text, int $len): array
    {
        $chunked = explode(PHP_EOL, $text);
        $paragraphs = [];
        foreach ($chunked as $line) {
            if (strlen($line) > $len) {
                $splitted = str_split($line, $len);
                $paragraphs = array_merge($paragraphs, $splitted);
                continue;
            }
            $paragraphs[] = $line;
        }
        return $paragraphs;
    }
}
