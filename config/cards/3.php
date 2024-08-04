<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('square < 3', fn(Code $code) => $code->square < 3),
    new Criterion('square = 3', fn(Code $code) => $code->square === 3),
    new Criterion('square > 3', fn(Code $code) => $code->square > 3),
]);