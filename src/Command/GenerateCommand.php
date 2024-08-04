<?php

namespace App\Command;

use App\DataObject\Code;
use App\DataObject\Criterion;
use App\DataObject\GeneratingPosition;
use App\DataObject\Problem;
use JetBrains\PhpStorm\NoReturn;
use function implode;

class GenerateCommand extends AbstractCommand
{
    const DEFAULT_WRONG = 4;
    private array $args;
    private array $options;

    public function __construct(string $configDir, array $args)
    {
        parent::__construct($configDir);
        [$this->args, $this->options] = $this->parseArgs($args, 0, [
            'load' => 1,
            'solve' => 0,
            'bicards' => 0,
            'extended' => 0,
            'wrong' => 1,
            'solution' => 0
        ]);
    }

    /**
     * @throws \Exception
     */
    #[NoReturn]
    public function run(bool $debug): void
    {
        if ($this->options['load']) {
            $problem = $this->engine->loadProblem($this->options['load']);
        } else {
            if (!$this->args) {
                $this->help();
            }
            $ncriteria = $this->args[0];
            if ($this->options['bicards']) {
                $problem = $this->engine->generateByCardProblem($ncriteria, 1);
            } elseif ($this->options['extended']) {
                $problem = $this->engine->generateExtendedProblem($ncriteria, $this->options['wrong'] ?: self::DEFAULT_WRONG, $solution);
            } else {
                $problem = $this->engine->generateByCardProblem($ncriteria, 0);
            }
        }

        if ($this->options['solution']) {
            $criteriaGroups = $problem->getCriteriaGroups();
        } else {
            $criteriaGroups = $problem->getShuffledCriteriaGroups();
        }

        foreach ($criteriaGroups as $n => $criteriaGroup) {
            $letter = chr(ord('A') + $n);
            echo "-------- $letter ------------\n";
            foreach ($criteriaGroup as $criterion) {
                if ($this->options['solution']) {
                    echo($n === 0 ? '* ' : '  ');
                }
                echo $criterion->getLabel() . '        (#' . $criterion->getCard() . ')' . "\n";
            }
        }
        echo "-----------------------\n";
        if ($this->options['solution']) {
            echo "=> " . $solution . "\n";
            echo "\n";
            // exit;
        }

        if ($this->options['solve']) {
            $this->interactiveSolve($problem);
        } else {
            echo "=> " . $this->engine->getProblemHash($problem) . "\n";
        }
        exit;
    }

    /**
     * @param Problem $problem
     */
    public function interactiveSolve(Problem $problem): void
    {
        while (true) {
            $line = readline("letter + code (ex : A227) ? ");

            $letters = [];
            $digits = [];
            foreach (str_split(strtoupper($line)) as $c) {
                if ($c >= '0' && $c <= '9') {
                    $digits [] = intval($c);
                } else {
                    $letters [] = $c;
                }
            }
            if ($digits) {
                $code = new Code($digits);
            }
            // else, keep old code

            foreach ($letters as $letter) {
                $n = ord($letter) - ord('A');
                $criterion = $problem->getCriteriaGroups()[$n][0];
                printf("For %s, the %s card (criterion: %s) say %s\n", $code, $letter, $criterion, ($criterion($code) ? 'YES' : 'NO'));
            }
            echo "\n";
        }
    }
}

