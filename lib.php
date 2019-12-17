<?php

/**
 * time_taken($tally=0, $precision=5)
 * 
 * Returns time in seconds.fraction at first call, and on every next call, the passed time since last call.
 * If $tally is 1, it also returns the total time passed since the first call, and optionally
 *   the passed time since last tally.
 * replace the <br> by a \n for logfile usage
**/
function time_taken($tally=0, $precision=5) {
  static $start = 0; // first call
  static $notch = 0; // tally calls
  static $time  = 0; // set to time of each call (after setting $duration)
  $now = microtime(1);
  if (! $start) { // init, basically
    $time = $notch = $start = $now;
    return "Starting at $start.\n";
  }
  $duration = $now - $time;
  $time = $now;
  $out = "That took ".round($duration, $precision)." seconds.\n";
  if ($tally) { // time passed since last tally
    $since_start      = $now - $start;
    $since_last_notch = $now - $notch;
    $notch = $now;
    $out .= "<br>\n". round($since_start, $precision) .' seconds since start'.($since_start!=$since_last_notch ? ' ('.round($since_last_notch, $precision) .' since last sum).':'.');
  }
  return $out;
}


function permutations(array $elements)
{
    if (count($elements) <= 1) {
        yield $elements;
    } else {
        foreach (permutations(array_slice($elements, 1)) as $permutation) {
            foreach (range(0, count($elements) - 1) as $i) {
                yield array_merge(
                    array_slice($permutation, 0, $i),
                    [$elements[0]],
                    array_slice($permutation, $i)
                );
            }
        }
    }
}

// this was hacked together drunkenly, maybe it could be shorter, but it works. it seems to work.
function prime_factors(int $x)
{
    if ($x < 4) return [$x];
    $sqrt = floor(sqrt($x));
    $i=2;
    $factors = [];
    while ($i <= $sqrt) {
        $a = $x / $i;
        if (is_int($a)) {
            $factors[] = $i;
            $x = $a;
        }else{
            if ($i >= $sqrt and $x != 1) $factors[] = $x;
            $i++;
        }
    }
    return $factors;
}

function reduce_fraction(int $x, int $y)
{
    $x_factors = prime_factors(abs($x));
    $y_factors = prime_factors(abs($y));
    foreach ($x_factors as $k => $factor) {
        if (! in_array($factor, $y_factors)) {
            unset($x_factors[$k]);
        }else{
            unset($y_factors[array_search($factor, $y_factors)]);
        }
    }
    $gcd = array_product($x_factors); // greatest common divisor
    return [$x/$gcd, $y/$gcd];
}

// returns degrees (yeah I know) from a line that would go from 0,0 to x,y
// degrees go from 0 (noon o'clock) to 359.9 (almost noon)
// I wrote this because it was past midnight and I hadn't had dinner yet and I couldn't figure out the one-liner.
function return_degrees($x, $y)
{
    settype($x, 'int');
    settype($y, 'int');
    if ($x > 0) {
        if($y === 0) {
            return 90;
        }elseif ($y > 0) {
            $ftn = $x / $y;
            return rad2deg(atan($ftn));
        }else{
            $ftn = $x / $y;
            return 180 + rad2deg(atan($ftn));
        }
    }elseif ($x === 0) {
        if ($y < 0) {
            return 180;
        }else{
            return 0;
        }
    }elseif($x < 0) {
        if ($y > 0) {
            $ftn = $x / $y;
            return 360 + rad2deg(atan($ftn));
        }elseif($y === 0) {
            return 270;
        }else{
            $ftn = $x / $y;
            return 180 + rad2deg(atan($ftn));
        }
    } // beer might have been involved
}

