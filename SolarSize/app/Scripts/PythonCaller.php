<?php

namespace App\Scripts;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonCaller{
    public static function callSolar($lat,$long,$timezone,$moduleTilt,$startDate,$endDate){
        
        $text = "The text you are desperate to analyze :)";
        $process = new Process(["python3", "/var/www/SolarSize/python_scripts/SolarCalc.py",$lat,$long,$timezone,$moduleTilt,$startDate,$endDate]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
        // Result (string): {'neg': 0.204, 'neu': 0.531, 'pos': 0.265, 'compound': 0.1779}
    }
}
