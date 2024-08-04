<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('square < 4', fn(Code $code) => $code->square < 4),
    new Criterion('square = 4', fn(Code $code) => $code->square === 4),
    new Criterion('square > 4', fn(Code $code) => $code->square > 4),
]);