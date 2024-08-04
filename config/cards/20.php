<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('one triple value', fn(Code $code) => $code->distinct() === 1),
    new Criterion('one double value', fn(Code $code) => $code->distinct() === 2),
    new Criterion('no repeated value', fn(Code $code) => $code->distinct() === 3),
]);