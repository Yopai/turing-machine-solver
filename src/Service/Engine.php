<?php

namespace App\Service;

use App\CardLoader;
use App\DataObject\ByCardGeneratingPosition;
use App\DataObject\Code;
use App\DataObject\Criterion;
use App\CriterionCard;
use App\DataObject\Problem;
use App\DataObject\GeneratingPosition;
use Exception;
use Generator;
use JetBrains\PhpStorm\NoReturn;
use function App\DataObject\array_union_all;

class Engine
{
    const NIGHTMARE_PREFIX = "‼"; // chr(19)
    const GENERATED_PREFIX = "☺"; // chr(1)
    private CardLoader $loader;
    public array $possibleCodes = [];
    /** @var CriterionCard[] */
    public array $cards = [];

    public function __construct(CardLoader $loader)
    {
        $this->loader = $loader;
        $this->possibleCodes = $this->getAllCodes();
    }

    public function init(array $cards): void
    {
        $this->cards = [];
        foreach ($cards as $card_number) {
            $this->cards [] = $this->loader->get($card_number);
        }
    }

    public function getAllCodes(): array
    {
        return array_map(
            fn($n) => new Code([
                intdiv($n, 25) % 5 + 1,
                intdiv($n, 5) % 5 + 1,
                $n % 5 + 1,
            ]),
            range(0, 124)
        );
    }

    public function isPossibleCode($code, $card): bool
    {
        foreach ($card->criteria as $criterion) {
            if ($criterion($code)) {
                return true;
            }
        }
        return false;
    }

    public function findPossible(): Generator
    {
        foreach ($this->getCriteriaCombinations($this->cards) as $criteriaCombination) {
            $correspondingCodes = $this->checkCriteriaCombination($criteriaCombination);

            // echo "*** Criteria combination [".implode(",", $correspondingCodes)."] : ".implode(" | ", $criteriaCombination)."\n";

            if (count($correspondingCodes) !== 1) {
                continue;
            }

            if ($this->isRedundant($criteriaCombination)) {
                continue;
            }

            yield (object)[
                'criteria' => $criteriaCombination,
                'code' => current($correspondingCodes),
            ];
        }
    }

    /**
     * @param array $cards
     * @param array $stack
     * @return Generator<Criterion[]>
     */
    public function getCriteriaCombinations(array $cards, array $stack = []): Generator
    {
        /*
          yield [
          $cards[0]->criteria[2],
          $cards[1]->criteria[2],
          $cards[2]->criteria[1],
          $cards[3]->criteria[1],
          $cards[4]->criteria[5],
          $cards[5]->criteria[3],
          ];

          return;
         */
        $card = array_pop($cards);
        foreach ($card->criteria as $criterion) {
            $newstack = array_merge($stack, [$criterion]);
            if (!$cards) {
                yield $newstack;
            } else {
                yield from $this->getCriteriaCombinations($cards, $newstack);
            }
        }
    }

    public function checkCriteriaCombination($criteriaCombination, $possibleCodes = null): array
    {
        $result = [];
        foreach ($possibleCodes ?? $this->possibleCodes as $code) {
            foreach ($criteriaCombination as $criterion) {
                if (!$criterion($code)) {
                    // this code don't satisfy every criterion
                    continue 2;
                }
            }
            $result [] = $code;
        }
        return $result;
    }

    public function getSubcombination($criteriaCombination): Generator
    {
        foreach (array_keys($criteriaCombination) as $key) {
            $tmp = $criteriaCombination;
            array_splice($tmp, $key, 1);
            $tmp = array_values($tmp);
            yield $tmp;
        }
    }

    #[NoReturn]
    public function debug($criteriaCombination, $codes): void
    {
        foreach ($codes as $code) {
            echo "Testing combination on $code :\n";
            foreach ($criteriaCombination as $criterion) {
                echo "  - $criterion : ";
                if (!$criterion($code)) {
                    echo " [KO]\n";
                } else {
                    echo " [OK]\n";
                }
            }
        }
        exit;
    }

    public function getLoader(): CardLoader
    {
        return $this->loader;
    }

    public function isRedundant(array $criteriaCombination): bool
    {
        foreach ($this->getSubcombination($criteriaCombination) as $subcombination) {
            $c2 = $this->checkCriteriaCombination($subcombination);
            // a subcombination still result in one code, so go on with combination
            // echo "  Criteria sub-combination [".implode(",", $correspondingCodes)."]: ".implode(" | - ", $subcombination)."\n";
            if (count($c2) === 1) {
                // echo "  => exclude this combination\n\n";
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $ncriteria
     * @param bool $nightmare
     * @return Problem
     * @throws Exception
     */
    public function generateByCardProblem(int $ncriteria, int $wrongCount, bool $nightmare = false): Problem
    {
        // all possible codes
        $possible = $this->getAllCodes();

        // all possible criteria
        $arrays = array_map(fn(CriterionCard $card) => $card->getCriteria(), $this->loader->getAll());
        $problem = (new ByCardGeneratingPosition($possible, array_union_all($arrays)))
            ->setWrongCardCount($wrongCount);
        foreach ($problem->getAllWithOneRandomCriterionMore($ncriteria) as $dto) {
            if (!$this->isRedundant($dto->getCriteria())) {
                return $dto->generateProblem($nightmare);
            }
        }
        throw new Exception ('Unable to generate a problem with these parameters');
    }

    /**
     * @param int $ncriteria
     * @param int $wrong
     * @param $solution
     * @param bool $nightmare
     * @return Problem
     * @throws Exception
     */
    public function generateExtendedProblem(int $ncriteria, int $wrong, &$solution, bool $nightmare = false): Problem
    {
        // all possible codes
        $possible = $this->getAllCodes();

        // all possible criteria
        $criteria = $this->getAllUniqueCriteria();

        $problem = new GeneratingPosition($possible, $criteria, []);
        foreach ($problem->getAllWithOneRandomCriterionMore($ncriteria) as $dto) {
            if (!$this->isRedundant($dto->getCriteria())) {
                $solution = $dto->getSolution();
                return $dto->generateProblem($nightmare, $wrong);
            }
        }
        throw new Exception ('Unable to generate a problem with these parameters');
    }

    public function getProblemHash(Problem $problem): string
    {
        $str = ($problem->isNightmare() ? self::NIGHTMARE_PREFIX : '')
            . ($problem->isGenerated() ? self::GENERATED_PREFIX : '')
            . implode(' ',
                array_map(
                    fn(array $criteriaGroup) => implode('', array_map(fn(Criterion $criterion) => $criterion->getId(), $criteriaGroup)),
                    $problem->getCriteriaGroups()
                )
            );
        $hash = base64_encode(gzcompress($str, 9));
        $hash = str_replace(['+', '/'], ['-', '_'], $hash);
        return $hash;
    }


    public function loadProblem(string $hash): Problem
    {
        $hash = str_replace(['-', '_'], ['+', '/'], $hash);
        $hash = gzuncompress(base64_decode($hash));
        $nightmare = str_starts_with($hash, self::NIGHTMARE_PREFIX);
        if ($nightmare) {
            $hash = substr($hash, strlen(self::NIGHTMARE_PREFIX));
        }
        $generated = str_starts_with($hash, self::GENERATED_PREFIX);
        if ($generated) {
            $hash = substr($hash, strlen(self::GENERATED_PREFIX));
        }

        return new Problem(
            array_map(
                fn($group_hash) => array_map(
                    fn($criteria_id) => $this->findCriteria($criteria_id),
                    str_split($group_hash, 2)
                ),
                explode(' ', $hash)
            ),
            $nightmare,
            $generated
        );
    }

    /**
     * @param null|CriterionCard[] $cards
     * @return Criterion[]
     */
    public function getAllCriteria(?array $cards = null): array
    {
        if (is_null($cards)) {
            $cards = $this->getLoader()->getAll();
        }
        $criteria = [];
        foreach ($cards as $card) {
            foreach ($card->getCriteria() as $criterion) {
                $criteria[] = $criterion;
            }
        }
        return $criteria;
    }

    /**
     * Same, deduplicated
     * @param null|CriterionCard[] $cards
     * @return Criterion[]
     */
    public function getAllUniqueCriteria(?array $cards = null): array
    {
        if (is_null($cards)) {
            $cards = $this->getLoader()->getAll();
        }
        $criteria = [];
        foreach ($cards as $card) {
            foreach ($card->getCriteria() as $criterion) {
                $criteria[$criterion->getLabel()] = $criterion;
            }
        }
        return array_values($criteria);
    }

    /**
     * @param string $criterion_id
     * @return Criterion
     * @throws Exception
     */
    private function findCriteria(string $criterion_id): Criterion
    {
        foreach ($this->getAllCriteria() as $criterion) {
            if ($criterion->getId() === $criterion_id) {
                return $criterion;
            }
        }

        debug_print_backtrace();
        echo "\n\n";
        header('Content-Type: text/plain');
        foreach ($this->getAllCriteria() as $criterion) {
            echo $criterion->getId() . ' : ' . $criterion . "\n";
        }
        throw new Exception("No criterion with id $criterion_id");
    }

    public function setupClassicProblem(array $cards, bool $nightmare)
    {
        $criteriaGroups = array_map(
            fn($ncard) => $this->loader->get($ncard)->getCriteria(),
            $cards
        );
        return new Problem($criteriaGroups, $nightmare, false);
    }

    public function setupExtremeProblem(array $cards, bool $nightmare)
    {
        $criteriaGroups = array_map(
            fn($bicard) => array_merge(
                $this->loader->get($bicard[0])->getCriteria(),
                $this->loader->get($bicard[1])->getCriteria(),
            ),
            $cards
        );
        return new Problem($criteriaGroups, $nightmare, false);
    }
}
