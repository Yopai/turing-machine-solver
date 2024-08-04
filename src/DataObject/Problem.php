<?php

namespace App\DataObject;

use App\Exception\PartialCodeException;

class Problem
{
    /** @var Criterion[][] */
    private array $criteriaGroups;
    private bool $nightmareMode;
    private bool $generated;

    /**
     * @param Criterion[][] $criteriaGroups
     * @param bool $nightmareMode
     * @param bool $generated
     */
    public function __construct(array $criteriaGroups, bool $nightmareMode, bool $generated)
    {
        $this->criteriaGroups = $criteriaGroups;
        $this->nightmareMode = $nightmareMode;
        $this->generated = $generated;
    }

    /**
     * @return Criterion[][]
     */
    public function getCriteriaGroups(): array
    {
        return $this->criteriaGroups;
    }

    /**
     * @return Criterion[][]
     */
    public function getShuffledCriteriaGroups(): array
    {
        // return a shuffled COPY of the array
        $array_shuffled = function ($array) {
            shuffle($array);
            return $array;
        };

        $result = array_map($array_shuffled, $this->criteriaGroups);
        if ($this->nightmareMode) {
            shuffle($result);
        }
        return $result;
    }

    public function getCriteriaIds(): array
    {
        return array_map(fn($group) => $group[0]->getId(), $this->criteriaGroups);
    }

    public function isNightmare(): bool
    {
        return $this->nightmareMode;
    }

    /**
     * @throws PartialCodeException
     */
    public function getAnswer(string $letter, Code $code)
    {
        $groupKey = ord($letter) - ord('A');
        if (strlen($letter) > 1 || $groupKey < 0 || $groupKey >= count($this->criteriaGroups)) {
            throw new \InvalidArgumentException("$letter is not a valid group letter");
        }
        $criterion = $this->criteriaGroups[$groupKey][0];
        return $criterion($code);
    }

    /**
     * @throws PartialCodeException
     */
    public function check(Code $code): bool
    {
        foreach ($this->criteriaGroups as $group) {
            $criterion = $group[0];
            if (!$criterion($code)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->generated;
    }

    public function setGenerated(bool $generated)
    {
        $this->generated = $generated;
    }
}