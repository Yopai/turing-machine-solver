<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle = 1', fn(Code $code) => $code->triangle === 1),
    new Criterion('triangle > 1', fn(Code $code) => $code->triangle > 1),
    new Criterion('square = 1', fn(Code $code) => $code->square === 1),
    new Criterion('square > 1', fn(Code $code) => $code->square > 1),
    new Criterion('circle = 1', fn(Code $code) => $code->circle === 1),
    new Criterion('circle > 1', fn(Code $code) => $code->circle > 1),
]);