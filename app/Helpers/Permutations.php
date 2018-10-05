<?php

namespace App\Helpers;


class Permutations
{
    public static function paired($arr)
    {
        $val1 = $arr[0];
        $pairs_per_set = sizeof($arr) / 2;
        foreach ($arr as $v1) {  // $arr is preserved/static
            $arr = array_slice($arr, 1);  // modify/reduce second foreach's $arr
            foreach ($arr as $v2) {
                if ($val1 == $v1) {
                    $first[] = [$v1, $v2];  // unique pairs as 2-d array containing first element
                } else {
                    $other[] = [$v1, $v2]; // unique pairs as 2-d array not containing first element
                }
            }
        }

        $perms = [];
        for ($i = 0; $i < $pairs_per_set; ++$i) {  // add one new set of pairs per iteration
            if ($i == 0) {
                foreach ($first as $pair) {
                    $perms[] = [$pair]; // establish an array of sets containing just one pair
                }
            } else {
                $expanded_perms = [];
                foreach ($perms as $set) {
                    $values_in_set = [];  // clear previous data from exclusion array
                    array_walk_recursive($set, function ($v) use (&$values_in_set) {
                        $values_in_set[] = $v;
                    }); // exclude pairs containing these values
                    $candidates = array_filter($other, function ($a) use ($values_in_set) {
                        return !in_array($a[0], $values_in_set) && !in_array($a[1], $values_in_set);
                    });
                    if ($i < $pairs_per_set - 1) {
                        $candidates = array_slice($candidates, 0, sizeof($candidates) / 2);  // omit duplicate causing candidates
                    }
                    foreach ($candidates as $cand) {
                        $expanded_perms[] = array_merge($set, [$cand]); // add one set for every new qualifying pair
                    }
                }
                $perms = $expanded_perms;  // overwrite earlier $perms data with new forked data
            }
        }
        return $perms;
    }

    public static function sampling(array $values, $size, $combinations = [])
    {
        # if it's the first iteration, the first set
        # of combinations is the same as the set of characters
        if (empty($combinations)) {
            $combinations = $values;
        }

        # we're done if we're at size 1
        if ($size == 1) {
            return $combinations;
        }

        # initialise array to put new values in
        $new_combinations = [];

        # loop through existing combinations and character set to create strings
        foreach ($combinations as $combination) {
            $combination = is_array($combination) ? $combination : [$combination];
            foreach ($values as $char) {
                $new_combinations[] = array_merge($combination, [$char]) ;
            }
        }
        # call same function again for the next iteration
        return self::sampling($values, $size - 1, $new_combinations);
    }

}