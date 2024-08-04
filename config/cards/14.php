<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle < square/circle', fn(Code $code) => $code->lowest('triangle')),
    new Criterion('square < triangle/circle', fn(Code $code) => $code->lowest('square')),
    new Criterion('circle < triangle/square', fn(Code $code) => $code->lowest('circle')),
]);