<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('triangle >= square/circle', fn(Code $code) => $code->greatest('triangle', false)),
    new Criterion('square >= triangle/circle', fn(Code $code) => $code->greatest('square', false)),
    new Criterion('circle >= triangle/square', fn(Code $code) => $code->greatest('circle', false)),
]);