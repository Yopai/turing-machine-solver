<?php

namespace App;

use App\Contract\CriterionCardInterface;
use App\DataObject\Code;
use App\DataObject\Criterion;
use Exception;

class CriterionCard implements CriterionCardInterface
{
    private array $criteria = [];
    private string $title;
    /** @var string[] */
    private array $symbols;

    /**
     * @param int|string[] $symbols
     * @param string $title
     * @param Criterion[] $criteria
     * @throws Exception
     */
    public function __construct(int|array $symbols, string $title, array $criteria)
    {
        foreach ($criteria as $k => $criterion) {
            if (!$criterion instanceof Criterion) {
                throw new Exception('Not a Criterion');
            }
            $criterion->setCard($this);
            $criterion->setId(str_pad(base_convert(intval($title) * 16 + $k, 10, 36), 2, '0', STR_PAD_LEFT));
        }
        $this->criteria = $criteria;
        $this->title = $title;
        $this->symbols = (is_int($symbols) ? array_slice(Code::SYMBOLS, 0, $symbols) : $symbols);
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
        return $this->criteria;
    }
}
