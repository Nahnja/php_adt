<?php

require_once __DIR__.'/../Arr.php';
require_once __DIR__.'/Test.php';


echo '<h1>Arr class</h1>';

section('array creation',
    subsection('',
        new Test(
            'new, range, from_array',
            [
                function() {
                    return expect($this->arr1 instanceof Arr, 'new Arr instanceof Arr')->to_be(true) &&
                    expect($this->arr2 instanceof Arr, 'Arr::range instanceof Arr')->to_be(true) &&
                    expect($this->arr2->to_a(), 'Arr::range vs range()')->to_be(range(-10,10,2));
                },
                function () {
                    $array = [0, ['a','b'], 2];
                    return expect(Arr::from_array($array), 'from_array recursive')->to_be(new Arr(0, new Arr('a','b'), 2)) &&
                    expect(Arr::from_array($array, false), 'from_array non-recursive')->to_be(new Arr(0, ['a','b'], 2));
                }
            ],
            function () {
                $this->arr1 = new Arr(1,2,3,'asdf',5,6,7);
                $this->arr2 = Arr::range(-10,10,2);
            }
        )
    )
);


section('array access',
    subsection('',
        new Test(
            'using brackets (get)',
            function() {
                $test_res = true;
                foreach ($this->native as $idx => $value) {
                    $test_res = $test_res && expect($this->arr[$idx])->to_be($value);
                }
                return $test_res &&
                expect($this->arr[-1])->to_be($this->native[count($this->native) - 1]);
            },
            function () {
                $this->native = [1,2,3,'asdf',5,true,7];
                $this->arr = new Arr(...$this->native);
            }
        ),
        new Test(
            'using brackets (set)',
            function() {
                $test_res = true;
                $new_vals = [1 => '2nd', -1 => 'last'];
                foreach ($new_vals as $idx => $value) {
                    $this->arr[$idx] = $value;
                    $test_res = $test_res && expect($this->arr[$idx])->to_be($new_vals[$idx]);
                }
                return $test_res;
            },
            function () {
                $this->native = [1,2,3,'asdf',5,true,7];
                $this->arr = new Arr(...$this->native);
            }
        ),
        new Test(
            'access convenience methods',
            function() {
                return
                expect($this->arr->first(), '$arr->first()')->to_be($this->native[0]) &&
                expect($this->arr->second())->to_be($this->native[1]) &&
                expect($this->arr->third())->to_be($this->native[2]) &&
                expect($this->arr->penultimate())->to_be($this->native[1]) &&
                expect($this->arr->last(), '$arr->last()')->to_be($this->native[2]);
            },
            function () {
                $this->native = [1,'asdf',true];
                $this->arr = new Arr(...$this->native);
            }
        )
    ),
    subsection('slicing w/ array',
        new Test(
            'positive valid indices',
            function() {
                return expect($this->arr[[1,3]])->to_be($this->arr->slice(1, 2)) &&
                expect($this->arr[[1,4]])->to_be($this->arr->slice(1, 3));
            },
            function () {
                $this->arr = new Arr(1,2,3,4,5,6,7);
            }
        ),
        new Test(
            'slicing w/ positive ints (array, string)',
            function() {
                $arr = new Arr(1,2,3,4,5);
                return expect($arr[[1,3]]->to_a())->to_be([2,3]) &&
                expect($arr['1:3']->to_a())->to_be([2,3]);
            }
        ),
        new Test(
            'slicing w/ negative ints (array, string)',
            function() {
                $arr = new Arr(1,2,3,4,5);
                return expect($arr[[-3,-1]]->to_a())->to_be([3,4]) &&
                expect($arr[' -3: -1']->to_a())->to_be([3,4]);
            }
        ),
        new Test(
            'slicing w/ invalid indices (array, string)',
            function() {
                $arr = new Arr();
                return expect(function() use ($arr) {return $arr[' a: -1'];})->to_throw() &&
                expect(function() use ($arr) {return $arr[['a', -1]];})->to_throw();
            }
        )
    )
);


section('Arr instance methods',
    subsection('automatically delegating to native methods (subset of array_* functions)',
        new Test(
            'chunk, column, count_values, diff, filter, intersect, keys, merge_recursive, pad, product, rand, reduce, replace_recursive, replace, slice, sum, udiff, uintersect, unique, values',
            [
                function() {
                    return expect($this->arr->chunk(2)->to_a(), 'chunk')->to_be(array_chunk($this->native, 2));
                },
                function() {
                    return expect($this->arr_2d->column(1)->to_a(), 'column')->to_be(array_column($this->native_2d, 1));
                },
                function() {
                    return expect($this->arr->count_values()->to_a(), 'count_values')->to_be(array_count_values($this->native));
                },
                function() {
                    return expect($this->arr->diff($this->arr_2d)->to_a(), 'diff')->to_be(array_diff($this->native, $this->native_2d));
                },
                function() {
                    $filter = function($e) {return $e;};
                    return expect($this->arr->filter($filter)->to_a(), 'filter')->to_be(array_filter($this->native, $filter));
                },
                function() {
                    return expect($this->arr->intersect($this->arr_2d)->to_a(), 'intersect')->to_be(array_intersect($this->native, $this->native_2d));
                },
                function() {
                    return expect($this->arr->keys()->to_a(), 'keys')->to_be(array_keys($this->native));
                },
                function() {
                    return expect($this->arr->merge_recursive($this->arr_2d)->to_a(), 'merge_recursive')->to_be(array_merge_recursive($this->native, $this->native_2d));
                },
                function() {
                    $size = 10;
                    $value = 'x';
                    return expect($this->arr->pad($size, $value)->to_a(), 'pad')->to_be(array_pad($this->native, $size, $value));
                },
                function() {
                    $n = range(1, 10);
                    $a = new Arr(...$n);
                    return expect($a->product(), 'product')->to_be(array_product($n));
                },
                function() {
                    $arr = Arr::range(1,50);
                    $first = $arr->rand();
                    $second = $arr->rand();
                    return expect($arr->has($first), 'rand')->to_be(true) &&
                    expect($arr->has($second), 'rand')->to_be(true) &&
                    expect($first == $second, 'rand (compare 2 rand values. run test again before checking code)')->to_be(false);
                },
                function() {
                    $initial = '>>';
                    $callback = function($prev, $cur) {
                        return $prev.'>'.$cur;
                    };
                    return expect($this->arr->reduce($callback, $initial), 'reduce')->to_be(array_reduce($this->native, $callback, $initial));
                },
                function() {
                    return expect($this->arr->replace_recursive($this->arr_2d)->to_a(), 'replace_recursive')->to_be(array_replace_recursive($this->native, $this->native_2d));
                },
                function() {
                    return expect($this->arr->replace($this->arr_2d)->to_a(), 'replace')->to_be(array_replace($this->native, $this->native_2d));
                },
                function() {
                    $start = 0;
                    $length = 2;
                    return expect($this->arr->slice($start, $length)->to_a(), 'slice')->to_be(array_slice($this->native, $start, $length));
                },
                function() {
                    $n = range(1, 100);
                    $a = new Arr(...$n);
                    return expect($a->sum(), 'sum')->to_be(array_sum($n));
                },
                function() {
                    $cb = function($a, $b) {
                        return __equals($a, $b);
                    };
                    return expect($this->arr->udiff($this->arr_2d, $cb), 'udiff')->to_be(array_udiff($this->native, $this->native_2d, $cb));
                },
                function() {
                    $cb = function($a, $b) {
                        return __equals($a, $b);
                    };
                    return expect($this->arr->uintersect($this->arr_2d, $cb), 'uintersect')->to_be(array_uintersect($this->native, $this->native_2d, $cb));
                },
                function() {
                    $res = true;
                    if (!expect($this->arr->unique(SORT_REGULAR)->to_a(), 'unique')->to_be(array_unique($this->native, SORT_REGULAR))) {
                        echo '...SORT_REGULAR failed';
                        $res = false;
                    }
                    if (!expect($this->arr->unique(SORT_NUMERIC)->to_a(), 'unique')->to_be(array_unique($this->native, SORT_NUMERIC))) {
                        echo '...SORT_NUMERIC failed';
                        $res = false;
                    }
                    if (!expect($this->arr->unique(SORT_STRING)->to_a(), 'unique')->to_be(array_unique($this->native, SORT_STRING))) {
                        echo '...SORT_STRING failed';
                        $res = false;
                    }
                    if (!expect($this->arr->unique(SORT_LOCALE_STRING)->to_a(), 'unique')->to_be(array_unique($this->native, SORT_LOCALE_STRING))) {
                        echo '...SORT_LOCALE_STRING failed';
                        $res = false;
                    }
                    return $res;
                },
                function() {
                    return expect($this->arr->values()->to_a(), 'values')->to_be(array_values($this->native));
                },
            ],
            function () {
                $this->native = [1,'asdf',true];
                $this->arr = new Arr(...$this->native);
                $this->native_2d = [1,[2, 'asdf'],true];
                $this->arr_2d = new Arr(...$this->native_2d);
            }
        )
    ),
    subsection('actually implemented methods (subset of array_* functions + more)',
        new Test('collection methods', [
            function() {
                $a = new Arr(1,2,3);
                $b = new Arr(1,2,3);
                $new_val = 'new_val';
                $a->add($new_val);
                $b->push($new_val);
                return expect($a, 'add')->to_be($b);
            },
            function() {
                return expect($this->arr, 'copy')->to_be($this->arr->copy());
            },
            function() {
                return expect($this->arr->copy()->clear(), 'clear')->to_be(new Arr());
            },
            function() {
                return expect($this->arr->copy()->equals($this->arr), 'equals')->to_be(true);
            },
            function() {
                return expect($this->arr->has('asdf'), 'has should')->to_be(true) &&
                expect($this->arr->has('asdf22'), 'has shouldnt')->to_be(false);
            },
            function() {
                return expect($this->arr->hash(), 'hash')->to_be($this->arr->copy()->hash()) &&
                expect($this->arr->hash(), 'hash')->not_to_be($this->arr_2d->hash());
            },
            function() {
                $a = $this->arr->copy();
                $a->remove('asdf');
                return expect($a->length, 'remove')->to_be($this->arr->length - 1);
            },
            function() {
                return expect($this->arr->size(), 'size (absolute)')->to_be(count($this->native)) &&
                expect($this->arr->size(), 'size (relative)')->to_be($this->arr->length);
            },
        ], function () {
            $this->native = [1,'asdf',true];
            $this->arr = new Arr(...$this->native);
            $this->native_2d = [1,[2, 'asdf'],true];
            $this->arr_2d = new Arr(...$this->native_2d);
        }),
        new Test('remaining methods', [
            function() {
                return expect($this->arr->concat($this->arr_2d)->to_a(), 'concat')->to_be(array_merge($this->native, $this->native_2d));
            },
            function() {
                return expect($this->arr->flatten(), 'flatten')->to_be($this->arr);
            },
            function() {
                $mapping = function($e) {
                    return __toString($e).'STRING';
                };
                $r = new Arr();
                foreach ($this->arr as $idx => $e) {
                    $r->push(__toString($e).'STRING');
                }
                return expect($this->arr->map($mapping), 'flatten')->to_be($r);
            },
            function() {
                $res = true;
                foreach ($this->arr as $idx => $elem) {
                    $res = $res && expect($this->arr->get($idx), 'get')->to_be($elem);
                }
                return $res;
            },
            function() {
                return expect($this->arr->copy()->merge($this->arr_2d)->to_a(), 'merge')->to_be(array_merge($this->native, $this->native_2d));
            },
            function() {
                $cloned_native = __clone($this->native);
                $removed_from_native = array_pop($cloned_native);
                $cloned_arr = $this->arr->copy();
                $removed_from_arr = $cloned_arr->pop();
                return expect($cloned_arr->to_a(), 'pop')->to_be($cloned_native) &&
                expect($removed_from_arr, 'pop (check return value)')->to_be($removed_from_native);
            },
            function() {
                $cloned_native = __clone($this->native);
                $cloned_arr = $this->arr->copy();

                $val = 'pushed value';
                $cloned_arr->push($val);
                array_push($cloned_native, $val);
                $val = 12;
                $cloned_arr[] = $val;
                array_push($cloned_native, $val);

                return expect($cloned_arr->to_a(), 'push (via ->push() and $arr[] =)')->to_be($cloned_native) &&
                expect($cloned_arr->size(), 'push (check length)')->to_be(count($cloned_native));
            },
            function() {
                return expect($this->arr->reversed()->to_a(), 'reversed')->to_be(array_reverse($this->native)) &&
                expect($this->arr->reversed() === $this->arr, 'reversed (check references)')->to_be(false) &&
                expect($this->arr->reversed()->reversed(), 'reversed (twice)')->to_be($this->arr);
            },
            function() {
                $cloned_native = __clone($this->native);
                $cloned_arr = $this->arr->copy();

                $new_ref = $cloned_arr->reverse();
                $cloned_native = array_reverse($cloned_native);

                return expect($cloned_arr->to_a(), 'reverse')->to_be($cloned_native) &&
                expect($cloned_arr === $new_ref, 'reverse (check references)')->to_be(true);
            },
            function() {
                return expect($this->arr->search('asdf'), 'search')->to_be($this->arr->index('asdf')) &&
                expect($this->arr->search('bsdf'), 'search')->to_be($this->arr->index('bsdf'));
            },
            function() {
                $cloned_native = __clone($this->native);
                $cloned_arr = $this->arr->copy();

                $new_ref = $cloned_arr->shift();
                array_shift($cloned_native);

                return expect($cloned_arr->to_a(), 'shift')->to_be($cloned_native);
            },
            function() {
                return expect($this->arr->copy()->shuffle()->to_a(), 'shuffle')->not_to_be($this->arr);
            },
            function() {
                $arr = new Arr(4,3,5,8,6,7,1,2,9,0);
                $arr2 = new Arr([2, 'c'], [0, 'a'], [2, 'd'], [5, 'z']);
                $cmp = function($a, $b) {
                    return $a[0] - $b[0];
                };
                return expect($arr->sort(), 'sort')->to_be(new Arr(0,1,2,3,4,5,6,7,8,9)) &&
                expect($arr2->sort($cmp), 'sort (stable)')->to_be(new Arr([0, 'a'], [2, 'c'], [2, 'd'], [5, 'z']));
            },
        ], function () {
            $this->native = [1,'asdf',true];
            $this->arr = new Arr(...$this->native);
            $this->native_2d = [1,[2, 'asdf'],true];
            $this->arr_2d = new Arr(...$this->native_2d);
        })
    )
);


section('class methods', subsection('for "range" and "from_array" see array creation section', new Test('', [
    function() {
        return expect(Arr::fill(5, 'x')->to_a(), 'fill')->to_be(array_fill(0, 5, 'x')) &&
        expect(Arr::fill(5)->to_a(), 'fill')->to_be(array_fill(0, 5, null));
    }
], function () {
    $this->native = [1,'asdf',true];
    $this->arr = new Arr(...$this->native);
    $this->native_2d = [1,[2, 'asdf'],true];
    $this->arr_2d = new Arr(...$this->native_2d);
})));


section('iteration', subsection('', new Test('foreach', [
    function() {
        $arr = new Arr(1,2,false,0,'0');
        $res = true;
        $expected_idx = 0;
        foreach ($arr as $idx => $value) {
            $res = $res && expect($idx, 'index')->to_be($expected_idx++) && expect($arr->get($idx), 'value')->to_be($value);
        }
        return $res;
    }
])));


?>
