<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Scripts\PythonCaller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/estimate',function(Request $request){
    $lat = (float)$request->lat;
    $long = (float)$request->long;
    $timezone = (int)$request->timezone;
    $moduleTilt = (int)$request->moduleTilt;
    $startDate = (string)$request->startDate;
    $endDate = (string)$request->endDate;

    

    // $lat = 49.6797747;
    // $long = -102.2334681;
    // $timezone = -6;
    // $moduleTilt = 30;
    //$startDate = "2021-06-25";
    //$endDate = "2021-06-26";
    $text = PythonCaller::callSolar($lat,$long,$timezone,$moduleTilt,$startDate,$endDate); 
    $re = '/(?<=\[).+?(?=\])/m';
    preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);


    $values[0] = explode(", ",$matches[0][0]);
    
    $values[1] = explode(", ",$matches[1][0]);

    for ($i = 0; $i < count($values[1]);$i++){
        $values[1][$i] = str_replace("'","",$values[1][$i]);
    }

    return $values;

    // $values = [
    // 0 => "0.0",
    // 1 => "0.0",
    // 2 => "0.0",
    // 3 => "0.0",
    // 4 => "1751.9175781235265",
    // 5 => "5705.50778631519",
    // 6 => "11061.934859046394",
    // 7 => "18651.186031760826",
    // 8 => "31965.677917329132",
    // 9 => "46167.88399891601",
    // 10 => "59042.39638186932",
    // 11 => "67787.77716970193",
    // 12 => "58539.42776613245",
    // 13 => "66344.37769962676",
    // 14 => "65453.10809350582",
    // 15 => "59462.04021004665",
    // 16 => "49654.77995148106",
    // 17 => "34090.595203478355",
    // 18 => "14546.853321928358",
    // 19 => "2180.406237983166",
    // 20 => "0.0",
    // 21 => "0.0",
    // 22 => "0.0",
    // 23 => "0.0",
    // 24 => "0.0",
    // 25 => "0.0",
    // 26 => "0.0",
    // 27 => "0.0",
    // 29 => "5688.729994962232",
    // 30 => "11080.456858066784",
    // 31 => "18792.853600647068",
    // 32 => "31395.480733299988",
    // 33 => "45156.13533868337",
    // 34 => "52720.04992477883",
    // 35 => "44171.419183256185",
    // 36 => "43330.701602892674",
    // 37 => "55714.04693017142",
    // 38 => "64172.271513162385",
    // 39 => "57908.951628421375",
    // 40 => "45088.99811159174",
    // 41 => "27556.31529877677",
    // 42 => "12928.04753931173",
    // 43 => "2063.7221960473116",
    // 44 => "0.0",
    // 45 => "0.0",
    // 46 => "0.0",
    // 47 => "0.0"
    // ];

    //dd(implode(":",$values));
    //dd($values);

    //$startDate = strtotime($startDate);
    //$endDate = strtotime($endDate);
    return $values;



});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
