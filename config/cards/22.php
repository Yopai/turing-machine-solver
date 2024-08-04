<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('ascending sequence', fn(Code $code) => $code->triangle < $code->square && $code->square < $code->circle),
    new Criterion('descending sequence', fn(Code $code) => $code->triangle > $code->square && $code->square > $code->circle),
    new Criterion('no sequence', function(Code $code) {
        if ($code->triangle < $code->square && $code->square < $code->circle) { return false; }
        if ($code->triangle > $code->square && $code->square > $code->circle) { return false; }
        return true;
    }),
]);