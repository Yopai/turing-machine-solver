<?php

namespace App;


use App\DataObject\Code;
use App\DataObject\Criterion;

return new CriterionCard(['triangle', 'square', 'circle'], pathinfo(__FILE__, PATHINFO_FILENAME), [
    new Criterion('3 consectuive ascending', function(Code $code) {
        return $code->triangle + 1 === $code->square
                && $code->square + 1 === $code->circle;
   
    }),
    new Criterion('2 consecutive ascending', function(Code $code) {
        return ($code->triangle + 1 === $code->square
                && $code->square + 1 !== $code->circle)
        || ($code->triangle + 1 !== $code->square
                && $code->square + 1 === $code->circle)
        ;
    }),
    new Criterion('no consecutive ascending', function(Code $code) {
        return ($code->triangle + 1 !== $code->square
                && $code->square + 1 !== $code->circle);
    }),
]);