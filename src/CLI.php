<?php

namespace Socodo\CLI;

use Socodo\CLI\Commands\HelpCommand;
use Socodo\CLI\Exceptions\CLIResolutionException;
use Socodo\CLI\Interfaces\CommandInterface;

class CLI
{
    /** @var string Executed script name. */
    protected string $bin;

    /** @var array Executed arguments. */
    protected array $executedArguments = [];

    /** @var array Executed options. */
    protected array $executedOptions = [];

    /** @var array<string, CommandInterface> Registered commands. */
    protected array $commands = [];

    /** @var array<string, array{
     *     name: string,
     *     description: string,
     *     shorthand: ?string
     *  }> Registered options. */
    protected array $options = [];

    /** @var array<string, string> Registered option shorthands. */
    protected array $optionShorthands = [];

    /**
     * Constructor.
     */
    public function __construct ()
    {
        $this->executedArguments = $this->getPhpArguments();
        $this->bin = basename(array_shift($this->executedArguments));
        $this->executedOptions = $this->getPhpOptions($this->executedArguments);
        $this->executedArguments = array_slice($this->executedArguments, 0, count($this->executedArguments) - count($this->executedOptions));
    }

    /**
     * Register some supporting commands.
     *
     * @return void
     */
    public function registerSupportingCommands (): void
    {
        $helpCommand = new HelpCommand();
        $helpCommand->setCommands($this->commands);
        $helpCommand->setOptions($this->options);
        $this->registerCommand($helpCommand);
    }

    /**
     * Register a command.
     *
     * @param CommandInterface|string $command Command instance or class name.
     * @return void
     */
    public function registerCommand (CommandInterface|string $command): void
    {
        if (is_string($command))
        {
            $command = new $command();
        }
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Register an option.
     *
     * @param string $name
     * @param string $description
     * @param string|null $shorthand
     * @return void
     */
    public function registerOption (string $name, string $description = '', string $shorthand = null): void
    {
        if (isset($this->options[$name]))
        {
            unset($this->optionShorthands[$this->options[$name]['shorthand']]);
            unset($this->options[$name]);
        }

        if ($shorthand !== null)
        {
            if (isset($this->optionShorthands[$shorthand]))
            {
                throw new CLIResolutionException(static::class . '::registerOption() Argument #3 ($shorthand) Given shorthand "' . $shorthand . '" has been already registered.');
            }

            $this->optionShorthands[$shorthand] = $name;
        }

        $this->options[$name] = [
            'name' => $name,
            'description' => $description,
            'shorthand' => $shorthand
        ];
    }

    /**
     * Handle execution.
     *
     * @return void
     */
    public function handle (): void
    {
        $arguments = $this->executedArguments;
        $action = array_shift($arguments);
        if ($action !== null)
        {
            $command = null;
            $segments = explode(':', $action);
            while (!empty($segments))
            {
                $segment = array_shift($segments);
                if ($command === null)
                {
                    $command = $this->commands[$segment];
                    continue;
                }

                $command = $command->getChildCommand($segment);
            }
        }

        if (!isset($command))
        {
            echo 'no command' . "\n";
            return;
        }

        $options = $this->executedOptions;

        $command->handle(new Writer(), $arguments, $options);
    }

    /**
     * Get raw PHP arguments array.
     *
     * @return array
     */
    public function getPhpArguments (): array
    {
        if (php_sapi_name() !== 'cli')
        {
            throw new CLIResolutionException(static::class . '::getPhpArguments() Cannot call this method on non-cli environment.');
        }

        global $argv;
        if (!is_array($argv))
        {
            if (!isset($_SERVER['argv']) || !is_array($_SERVER['argv']))
            {
                if (!isset($GLOBALS['HTTP_SERVER_VARS']['argv']) || !is_array($GLOBALS['HTTP_SERVER_VARS']['argv']))
                {
                    throw new CLIResolutionException(static::class . '::getPhpArguments() Cannot read command arguments. (Maybe register_argc_argv=Off)');
                }

                return $GLOBALS['HTTP_SERVER_VARS']['argv'];
            }

            return $_SERVER['argv'];
        }

        return $argv;
    }

    /**
     * Get executed options.
     *
     * @param array $arguments
     * @return array
     */
    public function getPhpOptions (array $arguments): array
    {
        $options = [];
        $arguments = array_reverse($arguments);
        foreach ($arguments as $argument)
        {
            if (!str_starts_with($argument, '-'))
            {
                break;
            }

            $options[] = $argument;
        }

        return array_reverse($options);
    }
}