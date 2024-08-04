<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle + square = 4', fn(Code $code) => $code->triangle + $code->square === 4),
    new Criterion('triangle + circle = 4', fn(Code $code) => $code->triangle + $code->circle === 4),
    new Criterion('square + circle = 4', fn(Code $code) => $code->square + $code->circle === 4),
]);