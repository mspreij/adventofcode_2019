<?php

class Intcode
{
    
    /** -- Methods -----------------------
     *                                            
     * __construct($file='')                      -- loads file (if any) into memory
     * init($program='', $input=0)                -- nice in loops: re-sets program from file or string, sets input, re-sets output, re-sets instruction_pointer
     * run()                                      -- sets instruction_pointer to 0, runs program. returns $output array after halt
     * set_memory_values(array $pairs)            -- [over]writes specific memory addresses with values
     * set_memory_value($address, $value)         -- [re-]sets a value at a memory address
     * get_memory_value($address, $mode=0)        -- get value from memory taking parameter mode into account:
                                                        For memory [3, 8, 4, 7, 5, 1, 13, 9, 12]
                                                        - 0 / positional is the value of the address at that address: get_memory_value(3, 0) => 9
                                                        - 1 / immediate  is the value at that address:                get_memory_value(3, 1) => 7
     * get_p_modes()                              -- returns array where [0] is the parameter mode for the first argument of current instruction, [1] for second etc.
     * get_input()                                
     * send_output($value='')                     -- stores input in ->output array.
     * load_file($file)                           -- used by construct, reads in program file and calls >load_data
     * load_data($data)                           -- called by >load_file, accepts string data, sets memory
     * load_instruction($opcode, $instruction)    -- each instruction must accept an Intcode object as parameter
     * 
     * 
    **/

    
    public $instruction_pointer = 0;
    public $memory = [];
    public $input  = [];
    public $output = [];
    protected $instructions = [];
    
    // Construct
    public function __construct($file='')
    {
        if (strlen($file)) {
            $this->load_file($file);
        }
    }
    
    public function init($program='', $input=[])
    {
        if (file_exists($program)) {
            $this->load_file($program);
        }elseif (strlen($program)){
            $this->load_data($program);
        }
        $this->input  = $input;
        $this->output = [];
        $this->instruction_pointer = 0; // for good measure
    }
    
    // --- run ---
    function run()
    {
        if (! count($this->memory)) throw new Exception("Program missing", 1);
        $this->instruction_pointer = 0; // init
        while (1) {
            $value = $this->memory[$this->instruction_pointer];
            $opcode = (int) substr($value, -2);
            // echo "$opcode\n";
            // possible to set parameter modes here, too
            if (isset($this->instructions[$opcode])) {
                $result = $this->instructions[$opcode]($this);
            }else{
                throw new Exception("unknown instruction: $opcode", 1);
            }
            if ($result === 'halt') break;
        }
        return $this->output;
    }
    
    // --- set_memory_values ---
    public function set_memory_values(array $pairs)
    {
        foreach ($pairs as $address => $value) {
            $this->memory[$address] = $value;
        }
    }
    
    // --- set_memory_value ---
    public function set_memory_value($address, $value)
    {
        $this->memory[$address] = $value;
    }
    
    public function get_memory_value($address, $mode=0)
    {
        $value = $this->memory[$address];
        if ($mode === 1) return $value; // mode 1: immediate mode
        return $this->memory[$value]; // mode 0: positional mode
    }
    
    // --- parameter modes ---
    public function get_p_modes()
    {
        $val = $this->memory[$this->instruction_pointer];
        return array_map('intval', str_split(strrev(str_pad(substr($val, 0, -2), 3, '0', STR_PAD_LEFT))));
    }
    
    public function get_input()
    {
        return array_shift($this->input);
    }
    
    public function send_output($value='')
    {
        $this->output[] = $value;
    }
    
    // --- load_file ---
    public function load_file($file)
    {
        $data = file_get_contents($file);
        return $this->load_data($data);
    }
    
    public function load_data($data)
    {
        $data = trim($data);
        if (! strlen($data)) throw new Exception("Program data empty", 1);
        $program = explode(',', $data);
        $this->memory = $program;
    }
    
    // --- load_instruction ---
    public function load_instruction($opcode, $instruction)
    {
        $this->instructions[$opcode] = $instruction;
    }
}

