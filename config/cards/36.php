<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('sum = 3n', fn(Code $code) => $code->sum % 3 === 0),
    new Criterion('sum = 4n', fn(Code $code) => $code->sum % 4 === 0),
    new Criterion('sum = 5n', fn(Code $code) => $code->sum % 5 === 0),
]);