<?php

namespace App;

use App\Contract\CriterionCardInterface;

class CardLoader
{

    private string $dir;
    /** @var CriterionCard[] */
    private array $loaded;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
        $this->loaded = [];
    }

    private function _load($number)
    {
        if (!isset($this->loaded[$number])) {
            $this->loaded[$number] = require("$this->dir/$number.php");
            ksort($this->loaded, SORT_NATURAL);
        }
        return $this->loaded[$number];
    }

    public function get(string $card_number): CriterionCardInterface
    {
        if (is_numeric($card_number)) {
            return $this->_load($card_number);
        }
        $cards = array_merge(array_map(
            fn($single_card_number) => $this->_load($single_card_number),
            explode('+', $card_number)
        ));
        return new CriterionMultiCard($cards, "Cards #$card_number");
    }

    public function getAll(): array
    {
        foreach (glob($this->dir . '/*.php') as $file) {
            $this->_load(pathinfo($file, PATHINFO_FILENAME));
        }
        return $this->loaded;
    }
}