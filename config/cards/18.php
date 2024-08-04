<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('sum is even', fn(Code $code) => ($code->sum() & 1) === 0),
    new Criterion('sum is odd', fn(Code $code) => ($code->sum() & 1) === 1),
]);