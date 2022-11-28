<?php

namespace Socodo\CLI\Commands;

use Socodo\CLI\Enums\Colors;
use Socodo\CLI\Interfaces\CommandInterface;
use Socodo\CLI\Writer;

class HelpCommand extends CommandAbstract
{
    /** @var string Command name. */
    protected string $name = 'help';

    /** @var string Command description. */
    protected string $description = 'Print all available commands.';

    /** @var array<string, CommandInterface> Registered commands. */
    protected array $commands = [];

    /** @var array Registered options. */
    protected array $options = [];

    /**
     * Set registered commands.
     *
     * @param array $commands
     * @return void
     */
    public function setCommands (array $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * Set registered options.
     *
     * @param array $options
     * @return void
     */
    public function setOptions (array $options): void
    {
        $this->options = $options;
    }

    /**
     * Handle execution.
     *
     * @param Writer $writer
     * @param array $arguments
     * @param array $options
     * @return void
     */
    public function handle (Writer $writer, array $arguments = [], array $options = []): void
    {
        $commandItems = $this->buildCommandItems($this->commands);
        $optionItems = $this->buildOptionItems($this->options);

        $maxLength = $writer->getMaxNameLengthFromIndex($commandItems);
        if ($maxLength < ($_ = $writer->getMaxNameLengthFromIndex($optionItems)))
        {
            $maxLength = $_;
        }

        $writer->color(Colors::LIGHT_BROWN, 'Commands:');
        $writer->index($commandItems, 0, $maxLength);
        $writer->write('');

        $writer->color(Colors::LIGHT_BROWN, 'Options:');
        $writer->index($optionItems, 0, $maxLength);
    }

    /**
     * Build index sheet items from command array.
     *
     * @param array $commands
     * @param string|null $parentName
     * @return array
     */
    protected function buildCommandItems (array $commands, string $parentName = null): array
    {
        $items = [];
        foreach ($commands as $command)
        {
            $name = ($parentName ? $parentName . ':' : '') . $command->getName();
            $item = [
                'name' => $name,
                'description' => $command->getDescription()
            ];

            $children = $command->getChildCommands();
            if (!empty($children))
            {
                $item['children'] = $this->buildCommandItems($children, $name);
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Build index sheet items from option array.
     *
     * @param array $options
     * @return array
     */
    protected function buildOptionItems (array $options): array
    {
        $items = [];
        foreach ($options as $option)
        {
            $name = '--' . $option['name'];
            if ($option['shorthand'] !== null)
            {
                $name = '-' . $option['shorthand'] . ', ' . $name;
            }

            $items[] = [
                'name' => $name,
                'description' => $option['description']
            ];
        }

        return $items;
    }
}