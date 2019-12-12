<pre><?php
require_once 'lib.php';
require_once 'intcode2.class.php';

// [2019-12-11 22:44:03] starting

echo time_taken()."\n";

$c = new IntCode2();
$c->debug = 1;

$data = '109,1,204,-1,1001,100,1,100,1008,100,16,101,1006,101,0,99';
$data = trim(file_get_contents('9.txt'));

$c->load_data($data);

$c->store_input(1);
$c->run();

$output = $c->output;

if (strlen($data) < 100) echo "\n\nData:   $data\n";
echo 'Output: '.join(',', $output);


?>