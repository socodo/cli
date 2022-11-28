<?php

namespace Socodo\CLI\Interfaces;

use Socodo\CLI\Writer;

interface CommandInterface
{
    /**
     * Get command name.
     *
     * @return string
     */
    public function getName (): string;

    /**
     * Get command description.
     *
     * @return string
     */
    public function getDescription (): string;

    /**
     * Get child commands.
     *
     * @return array<string, CommandInterface>
     */
    public function getChildCommands (): array;

    /**
     * Get a child command.
     *
     * @param string $name
     * @return CommandInterface|null
     */
    public function getChildCommand (string $name): ?CommandInterface;

    /**
     * Set child commands.
     *
     * @param array<string, CommandInterface> $children
     * @return void
     */
    public function setChildCommands (array $children): void;

    /**
     * Add a child command.
     *
     * @param CommandInterface $child
     * @return void
     */
    public function addChildCommand (CommandInterface $child): void;

    /**
     * Handle execution.
     *
     * @param Writer $writer
     * @param array $arguments
     * @param array $options
     * @return void
     */
    public function handle (Writer $writer, array $arguments = [], array $options = []): void;
}