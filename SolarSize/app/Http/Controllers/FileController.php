<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileController extends ApiController
{
      /** 
         * Recieves file and stores it in directory
         * @param Request $request The request containing the file to be uploaded
         * @return  json Returns a confirmation of uploading
     */ 

    function uploadFile(Request $request){

        $newFileName = time() . '.' . $request->file->getClientOriginalExtension();
        $path = storage_path("app/public/uploads");
        $request->file->move($path,$newFileName);
        return $this->respondWithData(['my_key' => $newFileName]);


    }


       /** 
         * Recieves request and deletes it in directory
         * @param Request $request The request containing the file to be deleted
         * @return  json Returns a confirmation of deletion
     */ 

    function deleteFile(Request $request){
        $path = storage_path("app/public/uploads");

        $data = $request->input("my_key");
        File::delete($path."/".$data);
        return $this->respondWithData("Successful Delete");


    }
    

    
}
