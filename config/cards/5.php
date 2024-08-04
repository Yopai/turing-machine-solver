<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle is even', fn(Code $code) => ($code->triangle & 1) === 0),
    new Criterion('triangle is odd', fn(Code $code) => ($code->triangle & 1) === 1),
]);