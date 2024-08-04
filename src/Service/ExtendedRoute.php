<?php

namespace App\Service;

class ExtendedRoute extends Route
{
    private array $params;

    public function matches($pathinfo): bool
    {
        $params = [];
        $paramname = '';
        $state = null;
        $k1 = 0;
        $k2 = 0;
        $str = $this->getPathinfo();
        while ($k1 < strlen($str)) {
            $c = $str[$k1];
            switch ($state) {
                case null:
                    if ($c === '{') {
                        $state = '{';
                        $k1++;
                    } elseif ($c !== $pathinfo[$k2]) {
                        return false;
                    } else {
                        $k1++;
                        $k2++;
                    }
                    break;
                case '{':
                    if ($c === '}') {
                        if (str_ends_with($paramname, '*')) {
                            $paramname = substr($paramname, 0, -1);
                            $params[$paramname] = substr($pathinfo, $k2);
                            $k2 = strlen($pathinfo);
                        } else {
                            $untilend = ($k1 + 1 >= strlen($str));
                            $params[$paramname] = '';
                            while ($k2 < strlen($pathinfo)
                                && $pathinfo[$k2] !== '/'
                                && ($untilend || $pathinfo[$k2] != $str[$k1 + 1])
                            ) {
                                $params[$paramname] .= $pathinfo[$k2];
                                $k2++;
                            }
                        }
                        $paramname = '';
                        $state = null;
                    } else {
                        $paramname .= $c;
                    }
                    $k1++;
                    break;
            }
        }
        if ($k2 < strlen($pathinfo)) {
            return false;
        }
        $this->params = $params;
        return true;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}