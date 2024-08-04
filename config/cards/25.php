<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('no consecutive sequence', fn(Code $code) => $code->countConsecutiveSeqUpOrDown() === 0),
    new Criterion('two consecutive digits', fn(Code $code) => $code->countConsecutiveSeqUpOrDown() === 2),
    new Criterion('three consecutive digits', fn(Code $code) => $code->countConsecutiveSeqUpOrDown() === 3),
]);
