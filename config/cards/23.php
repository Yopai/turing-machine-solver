<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('sum < 6', fn(Code $code) => $code->sum() < 6),
    new Criterion('sum = 6', fn(Code $code) => $code->sum() === 6),
    new Criterion('sum > 6', fn(Code $code) => $code->sum() > 6),
]);