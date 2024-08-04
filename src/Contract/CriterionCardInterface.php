<?php

namespace App\Contract;

use App\CriterionCard;
use Stringable;

interface CriterionCardInterface extends Stringable
{
    /**
     * @return CriterionCard[]
     */
     function getCriteria(): array;
}