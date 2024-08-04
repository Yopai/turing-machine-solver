<?php

namespace App\DataObject;

use App\CriterionCard;
use App\Exception\PartialCodeException;

class Criterion
{
    private string $id;
    private string $label;
    private \Closure $function;
    private CriterionCard $card;

    public function __construct(string $label, callable $function)
    {
        $this->label = strtr($label, [
            'triangle' => '▲',
            'square' => '■',
            'circle' => '●',
        ]);
        $this->function = $function;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function setCard(CriterionCard $card)
    {
        $this->card = $card;
    }

    /**
     * @param Code $code
     * @return bool
     * @throws PartialCodeException
     */
    public function __invoke(Code $code): bool
    {
        return call_user_func($this->function, $code);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCard(): CriterionCard
    {
        return $this->card;
    }

    public function hasId(): bool
    {
        return isset($this->id);
    }
}
