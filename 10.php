<pre>
<?php
require_once 'lib.php';
$input = trim(file_get_contents('10.txt'));

$input =
".#..#
.....
#####
....#
...##";

foreach (explode("\n", $input) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        // who knows, we might need this ¯\_(ツ)_/¯
        $grid[$y][$x] = $char;
        // make a list of asteroids anyway, we'll probably need those
        if ($char === '#') {
            $ast["$x:$y"] = ['sees'=>[], 'blocked'=>[]];
        }
    }
}
/*

Objective: find asteroid which can see the most other asteroids. When 3 asteroids are exactly on the same line (any angle), the first can't see the last.

How to get there: find the number of asteroids each asteroid can see.
Give an asteroid a "sees" array which stores other asteroids, and a "blocked" array which stores coordinates to ignore.

Notes:
- if it turns out A can see B, B can see A. Store that in B's >sees list too while scoring A.
- therefore you can get away with only checking the asteroids 'south' of the current one, if going from north to south (and on its own line, only east is interesting)
- an asteroid at [x,y] can see all asteroids on [x-1,y], [x+1,y], [x,y-1] and [x,y+1]. not sure this is really useful /yet/.

So.
- run through each asteroid ($ast[]) from north to south:
  - on its own line ($y), the first on $x+ it can see, add that (and add A to its >sees), then the rest is blocked and can be ignored
  - check all the asteroids below them, line by line:
    - when going through the x's on a line, Skip the ones in A->blocked. this can be true for the second and lower lines under A.
    - "A? if there is an asteroid in the checked line, YOU CAN SEE IT."
      - Add it to A->sees
      - add yourself to its >sees
      - now for the fun bit that makes the above true: figure out the x/y from A (current) to B, say it's relative -3/2. This means it can also Not see the block at -6/4. Buuuut, if the x/y was say 8/6, this doesn't just rule out 16/12 but also 12/9, so figure out the smallest step that gets you there (AKA reducing/simplifying fractions) and increment with that, until its x or y is outside the area range, to block out locations (A->blocked[] = those x/y's)



*/


?>