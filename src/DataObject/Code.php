<?php

namespace App\DataObject;

use App\Exception\PartialCodeException;

/**
 * @property-read int triangle
 * @property-read int square
 * @property-read int circle
 */
class Code
{
    const SYMBOLS = ['triangle', 'square', 'circle'];

    // value used to indicate this value is unknown
    const UNKNOWN = ' ';
    /** @var int[] */
    private array $values;

    public function __construct(array $values)
    {
        $nzint = function ($v) {
            return $v !== ' ' ? intval($v) : $v;
        };
        $this->values = [
            'triangle' => $nzint($values[0]),
            'square' => $nzint($values[1]),
            'circle' => $nzint($values[2]),
        ];
    }

    /**
     * @return int[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function __get($name)
    {
        if ($name === 'sum') {
            $this->checkValues();
            return array_sum($this->values);
        }
        $this->checkValue($name);
        return $this->values[$name];
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->values);
    }

    public function __toString()
    {
        return implode('', $this->values);
    }

    /*    public function count(int $sought): int
        {
            $this->checkValues();
            $result = 0;
            foreach ($this->values as $value) {
                if ($value === $sought) {
                    $result++;
                }
            }
            return $result;
        }*/

    public function countEqual(int $sought, int $expected): bool
    {
        $n = array_count_values($this->values);
        return $this->partialCountIsCompatible($n, $sought, $expected);
    }


    public function lowest($key, $strict = true): bool
    {
        if ($this->values[$key] === 1 && !$strict) {
            return true;
        }
        $this->checkValues();
        $value = $this->$key;
        foreach ($this->values as $k => $v) {
            if ($k !== $key && ($strict ? $v <= $value : $v < $value)) {
                return false;
            }
        }
        return true;
    }

    public function greatest($key, $strict = true): bool
    {
        if ($this->values[$key] === 5 && !$strict) {
            return true;
        }

        $this->checkValues();
        $value = $this->$key;
        foreach ($this->values as $k => $v) {
            if ($k !== $key && ($strict ? $v >= $value : $v > $value)) {
                return false;
            }
        }
        return true;
    }

    public function countParity($bit): int
    {
        $this->checkValues();
        $result = 0;
        foreach ($this->values as $value) {
            if ((intval($value) & 1) === $bit) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * @throws PartialCodeException
     */
    public function countParityEqual(int $bit, int $expected)
    {
        $n = $this->countParities();
        return $this->partialCountIsCompatible($n, $bit, $expected);
    }

    public function sum(?array $keys = null): int
    {
        if (!$keys) {
            $this->checkValues();
            return array_sum($this->values);
        }

        $result = 0;
        foreach ($keys as $key) {
            $this->checkValue($key);
            $result += $this->$key;
        }
        return $result;
    }

    public function distinct(): int
    {
        $this->checkValues();
        return count(array_count_values($this->values));
    }

    public function countConsecutiveSeqUpOrDown(): int
    {
        $this->checkValues();
        if ($this->triangle === $this->square - 1 && $this->square === $this->circle - 1) {
            return 3;
        }
        if ($this->triangle === $this->square + 1 && $this->square === $this->circle + 1) {
            return 3;
        }
        if ($this->triangle === $this->square - 1
            || $this->triangle === $this->square + 1
            || $this->square === $this->circle - 1
            || $this->square === $this->circle + 1
        ) {
            return 2;
        }
        return 0;
    }

    private function checkValue($name)
    {
        if ($this->values[$name] === self::UNKNOWN || $this->values[$name] === 0) {
            throw new PartialCodeException();
        }
    }

    private function checkValues()
    {
        if (array_search(self::UNKNOWN, $this->values, true)
            || array_search(0, $this->values, true)) {
            throw new PartialCodeException();
        }
    }

    /**
     * Check if, given the current code (that may be a partial one), the count of values in $n that equals $sought
     * may be compatible with $expected.
     * * if $sought is already found more than $expected times, return false
     * * if $sought + unknown is less than $expected times, return false
     * * else, throw an exception if there are unknown values
     * * return true if the count equals $expected, and there are no unknown value
     */
    private function partialCountIsCompatible(array $n, int $sought, int $expected)
    {
        if (($n[$sought] ?? 0) > $expected) {
            return false;
        }
        if (($n[$sought] ?? 0) + ($n[self::UNKNOWN] ?? 0) < $expected) {
            return false;
        }
        if ($n[self::UNKNOWN] ?? 0) {
            throw new PartialCodeException();
        }
        return true;
    }

    /**
     * Check if there are more digits of the given parity than ones of the other parity.
     * Throw an Exception if this is a partial code, and we can't determine with certainty
     * the greatest parity
     * * If there are already 2 even digits, greatestParity(0) can return true even if the third is unknown
     * * If there are already 2 odd digits, greatestParity(0) can return false even if the third is unknown
     * @param int $bit (0 for even, 1 for odd)
     * @return bool
     * @throws PartialCodeException
     */
    public function greatestParity(int $bit)
    {
        $n = $this->countParities();

        if ($n[$bit] > $n[1 - $bit] + $n[self::UNKNOWN]) {
            return true;
        }

        if ($n[$bit] + $n[self::UNKNOWN] < $n[1 - $bit]) {
            return false;
        }

        throw new PartialCodeException();
    }

    /**
     * @return int[]
     */
    public function countParities(): array
    {
        $n = [
            0 => 0,
            1 => 0,
            self::UNKNOWN => 0,
        ];
        foreach ($this->values as $value) {
            if ($value === self::UNKNOWN) {
                $n[self::UNKNOWN]++;
            } else {
                $n[$value & 1]++;
            }
        }
        return $n;
    }
}
