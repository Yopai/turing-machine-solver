<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(3, pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('even count > odd count', fn(Code $code) => $code->greatestParity(0)),
    new Criterion('even count < odd count', fn(Code $code) => $code->greatestParity(1)),
]);