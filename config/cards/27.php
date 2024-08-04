<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle < 4', fn(Code $code) => $code->triangle < 4),
    new Criterion('square < 4', fn(Code $code) => $code->square < 4),
    new Criterion('circle < 4', fn(Code $code) => $code->circle < 4),
]);