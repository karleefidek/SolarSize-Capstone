<?php

namespace App\Scripts;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonCaller{
    public static function callSolar($lat,$long,$timezone,$moduleTilt,$startDate,$endDate,$moduleArea,$moduleEfficiency,$lossCoefficient){
        
        $process = new Process(["python3", "../python_scripts/SolarCalc.py",$lat,$long,$timezone,$moduleTilt,$startDate,$endDate,$moduleArea,$moduleEfficiency,$lossCoefficient]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
