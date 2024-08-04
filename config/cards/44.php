<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('square < triangle', fn(Code $code) => $code->square < $code->triangle),
    new Criterion('square = triangle', fn(Code $code) => $code->square === $code->triangle),
    new Criterion('square > triangle', fn(Code $code) => $code->square > $code->triangle),
    new Criterion('square < circle', fn(Code $code) => $code->square < $code->circle),
    new Criterion('square = circle', fn(Code $code) => $code->square === $code->circle),
    new Criterion('square > circle', fn(Code $code) => $code->square > $code->circle),
]);