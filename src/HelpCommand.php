<?php

namespace App;

use App\Command\AbstractCommand;
use JetBrains\PhpStorm\NoReturn;

class HelpCommand extends AbstractCommand
{
    #[NoReturn]
    public function run(bool $debug): void
    {
        $this->help();
    }
}