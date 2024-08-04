<?php

namespace App;

use App\Contract\CriterionCardInterface;

class CriterionMultiCard  implements CriterionCardInterface {
    private $cards;
    private $title;

    public function __construct($cards, $title) {
        $this->cards = $cards;
        $this->title = $title;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @return CriterionCard[]
     */
    public function getCriteria(): array
    {
        return array_merge(array_map(fn(CriterionCardInterface $card) => $card->getCriteria(), $this->cards));
    }
}
