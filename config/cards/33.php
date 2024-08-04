<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle is even', fn(Code $code) => ($code->triangle & 1) === 0),
    new Criterion('triangle is odd', fn(Code $code) => ($code->triangle & 1) === 1),
    new Criterion('square is even', fn(Code $code) => ($code->square & 1) === 0),
    new Criterion('square is odd', fn(Code $code) => ($code->square & 1) === 1),
    new Criterion('circle is even', fn(Code $code) => ($code->circle & 1) === 0),
    new Criterion('circle is odd', fn(Code $code) => ($code->circle & 1) === 1),
]);