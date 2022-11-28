<?php

namespace Socodo\CLI\Commands;

use Socodo\CLI\Writer;

class BlankCommand extends CommandAbstract
{
    protected string $name = 'blank';
    protected string $description = 'Blank command.';

    public function handle (Writer $writer, array $arguments = [], array $options = []): void
    {
    }
}