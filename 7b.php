<pre><?php
require_once 'lib.php';
require_once 'intcode2.class.php';

echo time_taken()."\n";

$data = trim(file_get_contents('7.txt'));

$A = new IntCode2();
$B = new IntCode2();
$C = new IntCode2();
$D = new IntCode2();
$E = new IntCode2();

$A->output_target = $B;
$B->output_target = $C;
$C->output_target = $D;
$D->output_target = $E;
$E->output_target = $A;


$permutations = permutations([5, 6, 7, 8, 9]);

foreach($permutations as $phase_sequence) {
    // reset memory and set phase ..settings.
    $A->init($data, $phase_sequence[0]);
    $B->init($data, $phase_sequence[1]);
    $C->init($data, $phase_sequence[2]);
    $D->init($data, $phase_sequence[3]);
    $E->init($data, $phase_sequence[4]);
    
    $A->store_input(0);
    $A->run();
    $outputs[] = $A->input[0];
}

echo "Output: ". max($outputs)."\n";

/*

Values need to be pushed around.
And around.

A computer's state should possibly be "waiting for input" when it has an instruction 3 and nothing in the queue.
in that state, when another computer pokes it with a value, it should start running again.

That means that when a computer has OUTPUT, it should poke the next one, check if it's waiting for input, and if not uh, just print it or something? and carry on? This hopefully just means it was the last run in the loop.

All computers are initially waiting for input, so hitting run on the first one should make it run until it produces output - then the next one starts running. and so on in the loop, until it's poked by the last computer again, at which point it (the first) does not necessarily need input /yet/, but it just picks up where it paused and runs until it needs input and then it just keeps running and pokes the next one for output?

The instruction_pointer will change again after the poke, and the waiting output method in the call stack will, when finally getting a return, 

When a computer halts it should keep that status until an explicit 'run' is called again, maybe.
- it could also return the halt status to any computer that gave it input, if that's useful?
computers should have something where they can store their input and output computers. like SOCKETS. or network address names or something.

*/
?>