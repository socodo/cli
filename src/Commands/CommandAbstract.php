<?php

namespace Socodo\CLI\Commands;

use Socodo\CLI\Interfaces\CommandInterface;
use Socodo\CLI\Writer;

abstract class CommandAbstract implements CommandInterface
{
    /** @var string Command name */
    protected string $name;

    /** @var string Command description. */
    protected string $description;

    /** @var array<string, CommandInterface> Child commands. */
    protected array $childCommands = [];

    /**
     * Get command name.
     *
     * @return string
     */
    public function getName (): string
    {
        return $this->name;
    }

    /**
     * Get command description.
     *
     * @return string
     */
    public function getDescription (): string
    {
        return $this->description ?? $this->name . ' command.';
    }

    /**
     * Get child commands.
     *
     * @return array|CommandInterface[]
     */
    public function getChildCommands (): array
    {
        return $this->childCommands;
    }

    /**
     * Get a child command.
     *
     * @param string $name
     * @return CommandInterface|null
     */
    public function getChildCommand (string $name): ?CommandInterface
    {
        return $this->childCommands[$name] ?? null;
    }

    /**
     * Set child commands.
     *
     * @param array $children
     * @return void
     */
    public function setChildCommands (array $children): void
    {
        $this->childCommands = $children;
    }

    /**
     * Add a child command.
     *
     * @param CommandInterface $child
     * @return void
     */
    public function addChildCommand (CommandInterface $child): void
    {
        $this->childCommands[$child->getName()] = $child;
    }
}