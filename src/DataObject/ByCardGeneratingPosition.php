<?php

namespace App\DataObject;

use App\CriterionCard;
use const false;
use const true;

function array_union_all(array $arrays)
{
    $result = [];
    foreach ($arrays as $array) {
        foreach ($array as $item) {
            $result [] = $item;
        }
    }
    return $result;
}

class ByCardGeneratingPosition extends GeneratingPosition
{
    private int $wrongCardCount = 0;

    /**
     * @param int $wrongCardCount
     * @return $this
     */
    public function setWrongCardCount(int $wrongCardCount): static
    {
        $this->wrongCardCount = $wrongCardCount;
        return $this;
    }

    protected function createChild(array $possibleCodes, array $possibleCriteria, array $selectedCriteria): static
    {
        return (new static($possibleCodes, $possibleCriteria, $selectedCriteria))
            ->setWrongCardCount($this->wrongCardCount);
    }

    public function generateProblem(bool $nightmareMode, int $wrong = null): Problem
    {
        $groups = [];
        foreach ($this->getCriteria() as $rightCriterion) {
            $criteriaGroup = $this->buildCriteriaGroup($rightCriterion);

            if ($this->wrongCardCount) {
                foreach (range(1, $this->wrongCardCount) as $w) {
                    $wrongCriterion = $this->pickNewRandomCriterion();
                    $criteriaGroup = array_merge($criteriaGroup, $this->buildCriteriaGroup($wrongCriterion));
                }
            }
            $groups[] = $criteriaGroup;
        }
        return new Problem($groups, $nightmareMode, true);
    }

    /**
     * @param Criterion $rightCriterion
     * @return array
     */
    public function buildCriteriaGroup(Criterion $rightCriterion): array
    {
        $result = [];
        foreach ($rightCriterion->getCard()->getCriteria() as $criterion) {
            if ($criterion === $rightCriterion) {
                array_unshift($result, $criterion);
            } else {
                array_push($result, $criterion);
            }
        }
        return $result;
    }

    public function getPossibleCriteriaExcluding(Criterion $criterion): array
    {
        $cardCriteria = $criterion->getCard()->getCriteria();
        $result = [];
        foreach ($this->possibleCriteria as $item) {
            $found = false;
            foreach ($cardCriteria as $item2) {
                if ($item2 === $item) {
                    $found = true;
                }
            }
            if (!$found) {
                $result[] = $item;
            }
        }
        return $result;
    }
}