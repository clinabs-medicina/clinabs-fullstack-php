<?php
class ArrayList
{
    public static function Sum($array, $key) {
        $sum = 0;
        foreach ($array as $item) {
            $sum += $item[$key];
        }
        return $sum;
    }

    public static function Average($array, $key) {
        $sum = 0;
        $count = 0;
        foreach ($array as $item) {
            $sum += $item[$key];
            $count++;
        }
        return $sum / $count;
    }

    public static function Max($array, $key) {
        $max = 0;
        foreach ($array as $item) {
            if ($item[$key] > $max) {
                $max = $item[$key];
            }
        }
        return $max;
    }

    public static function Min($array, $key) {
        $min = 0;
        foreach ($array as $item) {
            if ($item[$key] < $min) {
                $min = $item[$key];
            }
        }
        return $min;
    }

    public static function Count($array, $key) {
        $count = 0;
        foreach ($array as $item) {
            if ($item[$key] > 0) {
                $count++;
            }
        }
        return $count;
    }

    public static function Unique($array, $key) {
        $unique = [];
        foreach ($array as $item) {
            if (!in_array($item[$key], $unique)) {
                $unique[] = $item[$key];
            }
        }
        return $unique;
    }

    public static function GroupBy($array, $key) {
        $group = [];
        foreach ($array as $item) {
            $group[$item[$key]][] = $item;
        }
        return $group;
    }

    public static function Distinct($array, $key) {
        $distinct = [];
        foreach ($array as $item) {
            if (!in_array($item[$key], $distinct)) {
                $distinct[] = $item[$key];
            }
        }
        return $distinct;
    }

    public static function Filter($array, $key, $value) {
        $filtered = [];
        foreach ($array as $item) {
            if ($item[$key] == $value) {
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
}