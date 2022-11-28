<?php

namespace Socodo\CLI;

use Socodo\CLI\Enums\Colors;

class Writer
{
    protected const PROGRESS_LENGTH = 60;
    protected const PROGRESS_FULL_CHAR = '#';
    protected const PROGRESS_EMPTY_CHAR = '.';
    protected const INDENT_LENGTH = 4;

    /** @var bool Determine if the environment is on CLI. */
    protected bool $isCli = false;

    /** @var bool Determine if progress printed. */
    protected bool $isProgress = false;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        if (php_sapi_name() === 'cli')
        {
            $this->isCli = true;

            $self = $this;
            register_shutdown_function(static function () use ($self) {
                if ($self->isProgress)
                {
                    echo "\n";
                }
            });
        }
    }

    /**
     * Print raw string.
     *
     * @param string $message
     * @return void
     */
    public function write (string $message): void
    {
        if (!$this->isCli)
        {
            return;
        }

        if ($this->isProgress)
        {
            $this->isProgress = false;
            echo "\n";
        }

        echo $message . "\n";
    }

    /**
     * Print colorized string.
     *
     * @param Colors $color
     * @param string $message
     * @return void
     */
    public function color (Colors $color, string $message): void
    {
        $this->write($this->colorize($color, $message));
    }

    /**
     * Colorize the string.
     *
     * @param Colors $color
     * @param string $message
     * @return string
     */
    public function colorize (Colors $color, string $message): string
    {
        return $color->value . $message . Colors::RESET->value;
    }

    /**
     * Print progress bar.
     *
     * @param int $current
     * @param int $max
     * @return void
     */
    public function progress (int $current, int $max): void
    {
        if (!$this->isCli)
        {
            return;
        }

        $length = floor(self::PROGRESS_LENGTH / $max * $current);
        $percent = floor($current / $max * 100);

        $message = '[';
        $message .= $this->colorize(Colors::BLUE, str_repeat(self::PROGRESS_FULL_CHAR, $length));
        $message .= $this->colorize(Colors::LIGHT_GRAY, str_repeat(self::PROGRESS_EMPTY_CHAR, self::PROGRESS_LENGTH - $length));
        $message .= ']';
        $message .= ' [' . $current . '/' . $max . ']';
        $message .= ' (' . $percent . '%)';
        $message .= "\r";

        $this->isProgress = true;
        echo $message;
    }

    /**
     * Print index sheet.
     *
     * @param array $items
     * @param int $indent
     * @param int $maxLength
     * @return void
     */
    public function index (array $items, int $indent = 0, int $maxLength = 0): void
    {
        if ($maxLength === 0)
        {
            $maxLength = $this->getMaxNameLengthFromIndex($items);
        }

        foreach ($items as $item)
        {
            $item['name'] = $item['name'] ?? '';
            $nameLength = mb_strlen($item['name']);

            $message = $this->getIndent($indent);
            $message .= '  ';
            $message .= $this->colorize(Colors::GREEN, $item['name']);
            $message .= str_repeat(' ', $maxLength - $nameLength - (($indent - 1) * self::INDENT_LENGTH));
            $messageLength = mb_strlen($message);

            $lines = explode("\n", $item['description'] ?? '');
            $message .= array_shift($lines);

            foreach ($lines as $line)
            {
                $message .= "\n";
                $message .= str_repeat(' ', $messageLength);
                $message .= $line;
            }

            $this->write($message);
            if (isset($item['children']))
            {
                $this->index($item['children'], $indent + 1, $maxLength);
            }
        }
    }

    /**
     * Get max length of name from index items.
     *
     * @param array $items
     * @return int
     */
    public function getMaxNameLengthFromIndex (array $items): int
    {
        $maxLength = 0;
        foreach ($items as $item)
        {
            $length = mb_strlen($item['name'] ?? '');
            if (isset($item['children']))
            {
                $childrenMaxLength = $this->getMaxNameLengthFromIndex($item['children']) + 8;
                if ($childrenMaxLength > $length)
                {
                    $length = $childrenMaxLength;
                }
            }

            if ($length > $maxLength)
            {
                $maxLength = $length;
            }
        }

        return $maxLength;
    }

    /**
     * Get indent string.
     *
     * @param int $count
     * @return string
     */
    protected function getIndent (int $count): string
    {
        return str_repeat(' ', $count * self::INDENT_LENGTH);
    }
}