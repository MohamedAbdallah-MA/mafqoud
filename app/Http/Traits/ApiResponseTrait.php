<?php
namespace App\Http\Traits;

trait ApiResponseTrait {
    public function apiResponse ($code = 200 , $message = null , $errors = null , $data = null ){

        $array = [
            'status' => $code ,
            'message' => $message ,
        ];

        if (is_null($data) && !is_null($errors)){
            $array['error'] = $errors ;
        }elseif (!is_null($data) && is_null($errors)){
            $array['data'] = $data ;
        }else {
            $array['error'] = $errors ;
            $array['data'] = $data ;
        }

        return response($array);

    }
}

?>
