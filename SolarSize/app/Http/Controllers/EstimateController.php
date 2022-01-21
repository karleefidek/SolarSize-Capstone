<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Scripts\PythonCaller;

class EstimateController extends ApiController
{

    

    /**
         * Returns that estimate data given a request
         * @param Request $request The request containing lat,long,time,tilt,startDate,endDate
         * @param string $interval  Interval we are calculating,
         *                          Can be "yearly","hourly","daily"
         *
         * @return array $values the array containing the generated estimates and graph data
     */ 


    function execute(Request $request){
        $lat = (float)$request->lat;
        $long = (float)$request->long;
        $timezone = (int)$request->timezone;
        $moduleTilt = (int)$request->moduleTilt;
        $startDate = (string)$request->startDate;
        $endDate = (string)$request->endDate;
    
        $text = PythonCaller::callSolar($lat,$long,$timezone,$moduleTilt,$startDate,$endDate); 
        $re = '/(?<=\[).+?(?=\])/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);
        $values[0] = explode(", ",$matches[0][0]);
        $values[1] = explode(", ",$matches[1][0]);
        for ($i = 0; $i < count($values[1]);$i++){
            $values[1][$i] = str_replace("'","",$values[1][$i]);
        }

        $values[2] = $this->getYearlyAverage($values[0]);
        
        return $values;
    
    
    
    }


       /**
         * Returns the amount of data point in a year, given an interval 
         *
         * @param string $interval  Interval we are calculating,
         *                          Can be "yearly","hourly","daily"
         *
         * @return int The number of data point in a year
     */ 

    protected function getIntervalYearlyValue($interval){
        switch($interval){
            case "yearly":
                return 1;
            case "daily":
                return 365;
            case "hourly":
            default:
                return 365*24;
        }
    }


    /**
         * Returns the average yearly average, given the datapoints and interval
         * @param array $powerData The array containing the power data
         * @param string $interval  Interval we are calculating,
         *                          Can be "yearly","hourly","daily"
         *
         * @return float The yearly average
     */ 

    function getYearlyAverage($powerData, $interval = "hourly"){
        
        $sum = 0;
        foreach($powerData as $dataPoint ){
            $sum+=(float)$dataPoint;
        }
        $totalIntervalPoints = $this->getIntervalYearlyValue($interval); 
        return $sum / (count($powerData) / $totalIntervalPoints);


    }
}
