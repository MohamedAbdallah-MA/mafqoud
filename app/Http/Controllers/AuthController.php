<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $authInterface ;

    public function __construct(AuthInterface $authInterface){
        $this->authInterface = $authInterface ;
    }

    public function register (Request $request)
    {
        return $this->authInterface->register($request);
    }
    

    public function login (Request $request)
    {
        return $this->authInterface->login($request);
    }
}
