<?php
/**
 * Returns time in seconds at first call, and on every next call, the passed time since last call.
 * If $tally is 1, it also returns the total time passed since the first call, and optionally
 *   the passed time since last tally.
**/

// replace the <br> by a \n for logfile usage

//_____________________________________
// time_taken($tally=0, $precision=5) /
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

