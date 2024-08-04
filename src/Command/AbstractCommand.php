<?php

namespace App\Command;

use App\CardLoader;
use App\DeckGenerator;
use App\Service\Engine;
use JetBrains\PhpStorm\NoReturn;

abstract class AbstractCommand
{
    protected Engine $engine;

    public function __construct(string $configDir)
    {
        $loader = new CardLoader($configDir . '/cards');
        $this->engine = new Engine($loader);
    }

    static function show($cards): void
    {
        foreach ($cards as $card) {
            echo implode(' | ', $card->criteria) . "\n";
        }
    }

    static function show_result($cards, $combinations): void
    {
        echo "========================================\n";
        self::show($cards);
        $n = count($combinations);
        $s = ($n > 1 ? 's' : '');
        $is = ($n > 1 ? 'are' : 'is');
        echo $n . " combination$s $is solution:\n";
        foreach ($combinations as $comb) {
            foreach ($comb->criteria as $criterion) {
                echo "   * $criterion\n";
            }
            echo "[=$comb->code]\n";
        }
    }

    protected function parseArgs(array $allArgs, int $requiredArgs, array $availableOptions): array
    {
        $args = [];
        $options = [];
        foreach ($availableOptions as $option => $n) {
            $options[$option] = match ($n) {
                0 => false,
                1 => null,
                default => []
            };
        }
        $opt = null;
        while (count($allArgs)) {
            $arg = array_shift($allArgs);
            if (str_starts_with($arg, '--')) {
                $opt = substr($arg, 2);
                if (array_key_exists($opt, $availableOptions)) {
                    if (str_contains($opt, '=')) {
                        [$opt, $value] = explode('=', $opt);
                        $options[$opt] = $value;
                        $opt = null;
                    } elseif ($availableOptions[$opt] === 0) {
                        $options[$opt] = true;
                        $opt = null;
                    }
                } else {
                    $this->help();
                }
            } else if ($opt) {
                $options[$opt] = $arg;
                $opt = null;
            } else {
                $args [] = $arg;
                $requiredArgs--;
            }
        }
        if ($opt || $requiredArgs > 0) {
            $this->help();
        }
        return [$args, $options];
    }

    #[NoReturn]
    public function help(): void
    {
        echo <<<TXT
tm    Turing Machine solver
  tm bruteforce [cards]
    Check the number of solutions for any combination, starting with <cards>.
      Stop after a certain number of combinations (to avoid memory_limit), and print the next cards on stderr.
  tm solve <cards>
    Search and show detailed solutions for <cards>
  tm hint <cards>
    Check the number of solutions for <cards>
  tm show <cards>
    Show the criteria of <cards>
  tm generate n
    Generate a problem with n cards
  tm generate --bicards n
    Generate a problem with [n] card couples
  tm generate --extended [--wrong m] n
    Generate a problem with <n> criteria. For each criterion, propose m wrong criteria (defaults to 2) 
  tm generate (...) --solution
    Also show the solution to the generated problem 
TXT;
        exit;
    }

    abstract public function run(bool $debug);
}
