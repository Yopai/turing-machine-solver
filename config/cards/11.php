<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle < square', fn(Code $code) => $code->triangle < $code->square),
    new Criterion('triangle = square', fn(Code $code) => $code->triangle === $code->square),
    new Criterion('triangle > square', fn(Code $code) => $code->triangle > $code->square),
]);