<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle < 3', fn(Code $code) => $code->triangle < 3),
    new Criterion('triangle = 3', fn(Code $code) => $code->triangle === 3),
    new Criterion('triangle > 3', fn(Code $code) => $code->triangle > 3),
]);