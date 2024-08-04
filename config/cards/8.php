<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no 1', fn(Code $code) => $code->countEqual(1, 0)),
    new Criterion('one 1', fn(Code $code) => $code->countEqual(1, 1)),
    new Criterion('two 1s', fn(Code $code) => $code->countEqual(1, 2)),
    new Criterion('three 1s', fn(Code $code) => $code->countEqual(1, 3)),
]);