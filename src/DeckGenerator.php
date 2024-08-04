<?php

namespace App;

/**
 * Generates a set of possible card combinations
 *
 * @author fello
 */
class DeckGenerator implements \Iterator
{

    private $k = 0;
    private $init = [];
    private $current = [];
    public $start = [];
    public $max = [];
    private $_revkeys = [];

    /**
     * "n" => force n
     * "[init:][start]-[end]"
     *    * start with init at the first iteration
     *    * restart at start on each successive iteration
     * "*" : generate automatically a number after the previous ones
     * Combinations are always ascending. So, at each iteration, a number will always start at minimal at the previous one + 1
     * @param string[] $initial
     */
    public function __construct($initial)
    {
        $this->init = $initial;
        $sorted = true;
        foreach ($initial as $k => $n) {
            if (!is_numeric($n)) {
                $sorted = true;
                break;
            }
            if ($k && $n < $initial[$k - 1]) {
                $sorted = false;
                break;
            }
        }
        if (!$sorted) {
            sort($this->init);
        }
        $this->rewind();
    }

    public function current(): mixed
    {
        return $this->current;
    }

    public function key(): mixed
    {
        return $this->k;
    }

    public function next(): void
    {
        if ($this->current === false) {
            return;
        }
        if (is_null($this->k)) {
            $this->k = 0;
        } else {
            $this->k++;
        }

        foreach ($this->_revkeys as $rk) {
            if ($this->current[$rk] < $this->max[$rk]) {
                $this->current[$rk]++;
                $this->ensureAsc();
                return;
            }

            $this->current[$rk] = $this->start[$rk];
        }

        $this->current = false;
    }

    public function rewind(): void
    {
        $this->k = 0;
        $this->current = [];
        $this->start = [];
        $this->max = [];
        $this->_revkeys = [];

        foreach ($this->init as $k => $value) {
            $this->current[$k] = ($k ? $this->current[$k - 1] + 1 : 1);
            $this->start[$k] = 1;
            $this->max[$k] = 48;
            array_unshift($this->_revkeys, $k);

            $this->current[$k] = $value;
            $this->start[$k] = 1;
            $this->max[$k] = 48;
        }

        // <n-1>th is max at <n>th max -1
        foreach (array_slice($this->_revkeys, 1) as $k) {
            $this->max[$k] = min($this->max[$k + 1] - 1, $this->max[$k]);
        }

        foreach ($this->current as $k => $value) {
            $this->current[$k] = max($this->start[$k], $this->current[$k]);
        }
        $this->ensureAsc();
    }

    public function valid(): bool
    {
        return ($this->current !== false);
    }

    public function ensureAsc()
    {
        foreach (array_slice(array_keys($this->current), 1) as $k) {
            $this->current[$k] = max($this->current[$k], $this->current[$k - 1] + 1);
        }
    }

}
