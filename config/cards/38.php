<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle + square = 6', fn(Code $code) => $code->triangle + $code->square === 6),
    new Criterion('triangle + circle = 6', fn(Code $code) => $code->triangle + $code->circle === 6),
    new Criterion('circle + square = 6', fn(Code $code) => $code->circle + $code->square === 6),
]);