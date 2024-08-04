<?php

namespace App\Command;

use App\DeckGenerator;

class GenericCommand extends AbstractCommand
{
    private string $action;
    private array $args;

    public function __construct(string $configDir, string $action, array $args)
    {
        parent::__construct($configDir);
        $this->action = $action;
        $this->args = $args;
    }

    public function run($debug): void
    {
        if (count($this->args) === 0) {
            $this->help();
        }

        if ($this->action === 'bruteforce') {
            $all_decks = new DeckGenerator($this->args);
        } else {
            $all_decks = [$this->args];
        }
        foreach ($all_decks as $i => $cards) {
            $this->engine->init($cards);
            if ($this->action === 'show') {
                self::show($cards);
                exit;
            }
            $solutions = iterator_to_array($this->engine->findPossible());

            switch ($this->action) {
                case 'hint':
                case 'bruteforce':
                    $n = count($solutions);
                    if ($n > 0 || $debug) {
                        echo implode(' ', $cards) . ": $n\n";
                    }
                    break;

                case 'solve':
                    self::show_result($this->engine->cards, $solutions);
                    break;
            }

            if ($i > ($debug ? 10 : 10000)) {
                $all_decks->next();
                if ($all_decks->valid()) {
                    fwrite(STDERR, implode(' ', $all_decks->current()));
                }
                exit;
            }
        }
    }
}