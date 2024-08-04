<?php

namespace App;

use App\Command\GenerateCommand;
use App\Command\AbstractCommand;
use App\Command\GenericCommand;

class ConsoleApp
{
    public function __construct(private string $configDir)
    {
    }

    public function createCommand($args): AbstractCommand
    {
        if (!$args) {
            return new HelpCommand($this->configDir);
        }
        $action = strtolower(array_shift($args));
        return $this->_createCommand($action, $args);
    }

    /**
     * @param string $action
     * @param $args
     * @return mixed
     */
    public function _createCommand(string $action, $args): AbstractCommand
    {
        return match ($action) {
            'generate'
            => new GenerateCommand($this->configDir, $args),
            'bruteforce',
            'solve',
            'hint',
            'show'
            => new GenericCommand($this->configDir, $action, $args),
            default
            => new HelpCommand($this->configDir),
        };
    }
}