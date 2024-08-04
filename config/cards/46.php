<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no 3', fn(Code $code) => $code->countEqual(3, 0)),
    new Criterion('one 3', fn(Code $code) => $code->countEqual(3, 1)),
    new Criterion('two 3s', fn(Code $code) => $code->countEqual(3, 2)),
    new Criterion('three 3s', fn(Code $code) => $code->countEqual(3, 3)),
    new Criterion('no 4', fn(Code $code) => $code->countEqual(4, 0)),
    new Criterion('one 4', fn(Code $code) => $code->countEqual(4, 1)),
    new Criterion('two 4', fn(Code $code) => $code->countEqual(4, 2)),
    new Criterion('three 4s', fn(Code $code) => $code->countEqual(4, 3)),
]);