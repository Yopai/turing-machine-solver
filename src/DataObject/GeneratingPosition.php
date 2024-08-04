<?php

namespace App\DataObject;

use Generator;
use function array_shift;
use function range;

class GeneratingPosition
{
    /** @var Code[] */
    private array $possibleCodes;

    /** @var Criterion[] */
    protected array $possibleCriteria;

    /** @var Criterion[] */
    private array $selectedCriteria = [];

    public function __construct(array $possibleCodes, array $possibleCriteria, array $selectedCriteria = [])
    {
        $this->possibleCodes = $possibleCodes;
        $this->possibleCriteria = $possibleCriteria;
        $this->selectedCriteria = $selectedCriteria;
    }

    protected function createChild(array $possibleCodes, array $possibleCriteria, array $selectedCriteria): static
    {
        return new static($possibleCodes, $possibleCriteria, $selectedCriteria);
    }

    /**
     * Recursively generates all possible dtos with (ncriteria) more criteria
     * Note : Can generate pseudo-solutions where criteria are redundant
     * @param int $ncriteria
     * @return Generator<self>
     */
    public function getAllWithOneRandomCriterionMore(int $ncriteria): Generator
    {
        shuffle($this->possibleCriteria);
        $k = 0;
        while ($k < count($this->possibleCriteria)) {
            $criterion = $this->possibleCriteria[$k];
            $possibleCodes = array_filter($this->possibleCodes, function (Code $code) use ($criterion) {
                return $criterion($code);
            });

            $n = count($possibleCodes);

            if ($n > 0 && $n !== count($this->possibleCodes)) {
                $dto = $this->getNewDto($possibleCodes, $criterion);

                if ($ncriteria === 1) {
                    if ($n === 1) {
                        yield $dto;
                    }
                } elseif (
                    // must keep something to do for every other criterion
                    $n >= pow(2, $ncriteria - 1)
                    // but reduce enough - to be defined, because some criteria can reduce drastically (ex : "two 3s")
                    /* && $n <= pow(5, $ncriteria - 1)*/) {
                    yield from $dto->getAllWithOneRandomCriterionMore($ncriteria - 1);
                }
            }
            $k++;
        }
    }

    public function getCriteria(): array
    {
        return $this->selectedCriteria;
    }

    public function getSolution(): ?Code
    {
        if (count($this->possibleCodes) !== 1) {
            return null;
        }
        return current($this->possibleCodes);
    }

    /**
     * @param array $possibleCodes
     * @param Criterion $criterion
     * @return self
     */
    public function getNewDto(array $possibleCodes, Criterion $criterion): GeneratingPosition
    {
        return $this->createChild(
            $possibleCodes,
            $this->getPossibleCriteriaExcluding($criterion),
            array_merge($this->selectedCriteria, [$criterion])
        );
    }

    public function generateProblem(bool $nightmareMode, int $wrong = null):Problem
    {
        $groups = [];
        foreach ($this->getCriteria() as $criterion) {
            $criteriaGroup = [$criterion];
            foreach (range(1, $wrong) as $ignored) {
                $criteriaGroup[] = $this->pickNewRandomCriterion();
            }
            $groups[] = $criteriaGroup;
        }
        return new Problem($groups, $nightmareMode, true);
    }

    /**
     * @param Criterion $criterion
     * @return Criterion[]
     */
    protected function getPossibleCriteriaExcluding(Criterion $criterion): array
    {
        $result = [];
        foreach ($this->possibleCriteria as $item) {
            if ($item !== $criterion) {
                $result[] = $item;
            }
        }
        return $result;
    }

    protected function pickNewRandomCriterion()
    {
        return array_shift($this->possibleCriteria);
    }
}