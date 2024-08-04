<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('circle is even', fn(Code $code) => ($code->circle & 1) === 0),
    new Criterion('circle is odd', fn(Code $code) => ($code->circle & 1) === 1),
]);