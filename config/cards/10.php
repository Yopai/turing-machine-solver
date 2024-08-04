<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no 4', fn(Code $code) => $code->countEqual(4, 0)),
    new Criterion('one 4', fn(Code $code) => $code->countEqual(4, 1)),
    new Criterion('two 4s', fn(Code $code) => $code->countEqual(4, 2)),
    new Criterion('three 4s', fn(Code $code) => $code->countEqual(4, 3)),
]);