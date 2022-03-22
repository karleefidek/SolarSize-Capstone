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


    function executeNoPanels(Request $request)
    {
        $lat = (float)$request->lat;
        $long = (float)$request->long;
        $timezone = (int)$request->timezone;
        $moduleTilt = (int)$request->moduleTilt;
        $startDate = (string)$request->startDate;
        $endDate = (string)$request->endDate;
        $panelDirection = (float)$request->panelDirection;
        $moduleArea = (float)$request->moduleArea;
        $moduleEfficiency = (float)$request->moduleEfficiency;
        $lossCoefficient = (float)$request->lossCoefficient;


        $text = PythonCaller::callSolarNoPanels($lat, $long, $timezone, $moduleTilt, $startDate, $endDate, $moduleArea, $moduleEfficiency, $lossCoefficient);
        $re = '/(?<=\[).+?(?=\])/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);
        $values[0] = explode(", ", $matches[0][0]);
        $values[1] = explode(", ", $matches[1][0]);
        for ($i = 0; $i < count($values[1]); $i++) {
            $values[1][$i] = str_replace("'", "", $values[1][$i]);
        }

        $values[2] = $this->getYearlyAverage($values[0]);

        return $values;
    }


    function executePanels(Request $request)
    {
        $lat = (float)$request->lat;
        $long = (float)$request->long;
        $timezone = (int)$request->timezone;
        $moduleTilt = (int)$request->moduleTilt;
        $startDate = (string)$request->startDate;
        $endDate = (string)$request->endDate;
        $requestArray["SolarPanels"] = $request->panels;
        $solarPanelJson = json_encode($requestArray);
        $text = PythonCaller::callSolarWithPanels($lat, $long, $timezone, $moduleTilt, $startDate, $endDate, $solarPanelJson);
        $re = '/(?<=\[).+?(?=\])/m';
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);
        $matchesIndex = 0;
        for ($i = 1; $matchesIndex < count($matches); $i++) {
            $values[$i]["Name"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["TimeZone"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["Area"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["Cost"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["Wattage"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["Power"] = explode(", ", $matches[$matchesIndex++][0]);
            $values[$i]["Dates"] = explode(", ", $matches[$matchesIndex++][0]);
            for ($j = 0; $j < count($values[$i]["Dates"]); $j++) {
                $values[$i]["Dates"][$j] = str_replace("'", "", $values[$i]["Dates"][$j]);
            }
        }


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

    protected function getIntervalYearlyValue($interval)
    {
        switch ($interval) {
            case "yearly":
                return 1;
            case "daily":
                return 365;
            case "hourly":
            default:
                return 365 * 24;
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

    function getYearlyAverage($powerData, $interval = "hourly")
    {

        $sum = 0;
        foreach ($powerData as $dataPoint) {
            $sum += (float)$dataPoint;
        }
        $totalIntervalPoints = $this->getIntervalYearlyValue($interval);
        return $sum / (count($powerData) / $totalIntervalPoints);
    }
}
