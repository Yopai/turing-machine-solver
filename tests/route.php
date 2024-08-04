<?php

use App\Service\ExtendedRoute;

require __DIR__ . '/../vendor/autoload.php';

function ve($v): array|string|null
{
    $result = str_replace("\n", '', var_export($v, true));
    return preg_replace(pattern: '/ *\( */', replacement: '(', subject: $result);
}

function testroute($route, $pathinfo, $expected = []): bool
{
    $route = new ExtendedRoute($route, fn() => null);
    $actual = $route->matches($pathinfo) ? $route->getParams() : false;
    if ($actual !== $expected) {
        echo "[**]";
    } else {
        echo "[ok]";
    }
    echo '  expected ' . ve($expected) . ', got ' . ve($actual)."\n";
    return ($actual === $expected);
}

testroute('toto/{id}', 'toto/12', ['id' => '12']);
testroute('toto/{id}/tutu/{abc*}', 'toto/12/14/tutu/x/z', false);
testroute('toto/{id}/tutu/{abc*}', 'toto/14/tutu/x/z', ['id' => '14', 'abc' => 'x/z']);
