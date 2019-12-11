<pre><?php
require_once 'lib.php';
require_once 'intcode2.class.php';

echo time_taken()."\n";
$c = new IntCode2();

$data = trim(file_get_contents('7.txt'));

try {
    foreach(permutations([0, 1, 2, 3, 4]) as $phase_sequence) {
        $input = 0;
        foreach ($phase_sequence as $phase) {
            $c->init($data, [$phase, $input]);
            $output = $c->run();
            $input = $output[0];
        }
        $out[] = $input;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

echo "Output: ". max($out)."\n";

echo time_taken()."\n";



?>