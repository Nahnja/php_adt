<?php

function equals($x, $y) {
    if (method_exists($x, "equals")) {
        return $x->equals($y);
    }
    if (method_exists($y, "equals")) {
        return $y->equals($x);
    }
    return $k == $key;
}

function compare($x, $y) {
    if (method_exists($x, "compare")) {
        return $x->compare($y);
    }
    if (method_exists($y, "compare")) {
        return $y->compare($x);
    }
    if (equals($x, $y)) {
        return 0;
    }
    throw new Exception("Given parameters are not comparable!", 1);
}

function hash($x) {
    if (method_exists($x, "hash")) {
        return $x->hash();
    }
    if (is_numeric($x)) {
        return (float) $x;
    }
    if (is_string($x)) {
        // TODO: calc string hash
    }
    if (is_bool($x)) {
        return (int) $x;
    }
    // if ($x === null) {
    //     return 0;
    // }
    throw new Exception("Given parameter does not support hashing!", 1);
}

function mergesort_compare($a, $b) {
    return compare($a, $b);
}

// for Arr instance
function mergesort(&$array, $cmp_function = 'mergesort_compare') {
    // Arrays of size < 2 require no action.
    if (count($array) < 2)
        return;

    // Split the array in half
    $halfway = count($array) / 2;
    $array1 = array_slice($array, 0, $halfway);
    $array2 = array_slice($array, $halfway);
    // Recurse to sort the two halves
    mergesort($array1, $cmp_function);
    mergesort($array2, $cmp_function);
    // If all of $array1 is <= all of $array2, just append them.
    if (call_user_func($cmp_function, end($array1), $array2[0]) < 1) {
        $array = array_merge($array1, $array2);
        return;
    }
    // Merge the two sorted arrays into a single sorted array
    $array = array();
    $ptr1 = $ptr2 = 0;
    while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
        if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2]) < 1) {
            $array[] = $array1[$ptr1++];
        }
        else {
            $array[] = $array2[$ptr2++];
        }
    }
    // Merge the remainder
    while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
    while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];
    // return;
}

// for native array
function mergesort(&$array, $cmp_function = 'strcmp') {
    // Arrays of size < 2 require no action.
    if (count($array) < 2)
        return;

    // Split the array in half
    $halfway = count($array) / 2;
    $array1 = array_slice($array, 0, $halfway);
    $array2 = array_slice($array, $halfway);
    // Recurse to sort the two halves
    mergesort($array1, $cmp_function);
    mergesort($array2, $cmp_function);
    // If all of $array1 is <= all of $array2, just append them.
    if (call_user_func($cmp_function, end($array1), $array2[0]) < 1) {
        $array = array_merge($array1, $array2);
        return;
    }
    // Merge the two sorted arrays into a single sorted array
    $array = array();
    $ptr1 = $ptr2 = 0;
    while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
        if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2]) < 1) {
            $array[] = $array1[$ptr1++];
        }
        else {
            $array[] = $array2[$ptr2++];
        }
    }
    // Merge the remainder
    while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
    while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];
    // return;
}


?>
