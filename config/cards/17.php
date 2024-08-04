<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no even digit', fn(Code $code) => $code->countParityEqual(0, 0)),
    new Criterion('one even digit', fn(Code $code) => $code->countParityEqual(0, 1)),
    new Criterion('two even digits', fn(Code $code) => $code->countParityEqual(0, 2)),
    new Criterion('three even digits', fn(Code $code) => $code->countParityEqual(0, 3)),
]);