<?php
namespace App\Http\Interfaces;


interface AuthInterface {

    public function register($request);
    public function login($request);
    public function generateOtpCode($request);
    public function checkOtpCode($request);
    public function resetPassword($request);
}

