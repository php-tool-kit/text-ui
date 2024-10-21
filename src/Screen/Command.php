<?php

namespace TextUI\Screen;

/**
 * Sends ANSI commands to the terminal.
 *
 * @author Everton
 */
class Command
{
    /**
     * Clear screen.
     */
    public const string CLEAR_SCREEN = "\x1b[H\x1b[2J";

    /**
     * Deletes the entire line.
     */
    public const string CLEAR_LINE = "\x1b[2K";

    /**
     * Moves the cursor to the beginning of the line.
     */
    public const string GOTO_LINE_START = "\r";

    /**
     * Sends an ANSI command to the terminal.
     *
     * @param string $command The ANSI command, including ESC[, to be sent to
     * the terminal.
     * @return void
     */
    public static function send(string $command): void
    {
        echo $command;
    }
}
