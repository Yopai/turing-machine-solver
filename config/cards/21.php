<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no twin value', fn(Code $code) => $code->distinct() !== 2),
    new Criterion('two twin values', fn(Code $code) => $code->distinct() === 2),
]);