<?php
$orig = $input = explode(',', trim(file_get_contents('2.txt')));

// before running the program, replace position 1 with the value 12 and replace position 2 with the value 2.
// What value is left at position 0 after the program halts?
// part 1
$part = 2;
if ($part == 1) {
    $input[1] = 12;
    $input[2] = 2;
    for ($i=0; $i < count($input); $i+=4) {
        $oc = $input[$i];
        if ($oc == 1) $input[$input[$i+3]] = $input[$input[$i+1]] + $input[$input[$i+2]]; // wtfbbq
        if ($oc == 2) $input[$input[$i+3]] = $input[$input[$i+1]] * $input[$input[$i+2]];
        if ($oc == 99) break;
    }
    echo $input[0];
}else{
    // read the entire thing again but do it sober because fukkin'ell
    // part 2
    for ($noun=0; $noun < 100; $noun++) {
        for ($verb=0; $verb < 100; $verb++) {
            $input = $orig;
            $input[1] = $noun;
            $input[2] = $verb;
            for ($i=0; $i < count($input); $i+=4) {
                $oc = $input[$i];
                if ($oc == 1) $input[$input[$i+3]] = $input[$input[$i+1]] + $input[$input[$i+2]]; // wtfbbq
                if ($oc == 2) $input[$input[$i+3]] = $input[$input[$i+1]] * $input[$input[$i+2]];
                if ($oc == 99) break;
            }
            if ($input[0] == 19690720) {
                die("x $noun $verb ". ($noun * 100 + $verb));
            }
        }
    }
}

?>