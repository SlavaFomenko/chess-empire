<?php

namespace Handy\Console;

class Console
{

    const COLORS = [
        'black'   => 0,
        'red'     => 1,
        'green'   => 2,
        'yellow'  => 3,
        'blue'    => 4,
        'magenta' => 5,
        'cyan'    => 6,
        'white'   => 7
    ];

    const STYLES = [
        'reset'      => 0,
        'bold'       => 1,
        'dim'        => 2,
        'underlined' => 4,
        'blink'      => 5,
        'reverse'    => 7,
        'hidden'     => 8
    ];

    /**
     * @param $bg
     * @param $fg
     * @param $style
     * @return void
     */
    public static function style($bg = null, $fg = null, $style = null): void
    {
        if ($bg !== null) {
            echo "\e[48;5;" . self::COLORS[$bg] . "m";
        }
        if ($fg !== null) {
            echo "\e[38;5;" . self::COLORS[$fg] . "m";
        }
        if ($style !== null) {
            echo "\e[" . self::STYLES[$style] . "m";
        }
    }

    /**
     * @param $message
     * @return void
     */
    public static function warning($message): void
    {
        self::style(fg: 'yellow', style: 'bold');
        self::write("Warning: $message");
        self::resetStyle();
    }

    /**
     * @param $message
     * @return void
     */
    public static function error($message): void
    {
        self::style(fg: 'red', style: 'bold');
        self::write("Error: $message");
        self::resetStyle();
    }

    /**
     * @param $text
     * @param string $bg
     * @param string $fg
     * @return void
     */
    public static function banner($text, $bg = 'green', $fg = 'black'): void
    {
        $consoleWidth = exec('tput cols');

        self::style(bg: $bg, fg: $fg, style: 'bold');

        self::write();
        self::write(str_pad('    ' . $text, $consoleWidth));
        self::resetStyle();
        self::write();
    }

    /**
     * @return void
     */
    public static function resetStyle(): void
    {
        echo "\e[0m";
    }

    /**
     * @param string $text
     * @return void
     */
    public static function write(string $text = ""): void
    {
        echo $text . PHP_EOL;
    }

    /**
     * @return false|string
     */
    public static function read(): false|string
    {
        return readline();
    }

    /**
     * @param $description
     * @param $validationCallback
     * @return string
     */
    public static function ask($description, $validationCallback = null): string
    {
        $validationCallback = $validationCallback ?: fn() => true;
        self::write($description . ": ");
        do {
            self::resetStyle();
            echo "> ";
            $response = trim(self::read());
            if (!$validationCallback($response)) {
                self::error("Invalid data");
            }
        } while (!$validationCallback($response));

        return $response;
    }

    /**
     * @param $msg
     * @return bool
     */
    public static function submit($msg): bool
    {
        $validationCallback = function ($a) {
            return in_array(strtolower($a), [
                "yes",
                "y",
                "no",
                "n"
            ]);
        };

        return in_array(Console::ask($msg . " (yes/no)", $validationCallback), [
            "yes",
            "y"
        ]);
    }
}

