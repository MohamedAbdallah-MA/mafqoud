<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UserInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface ;
    }

    public function getUsers()
    {
        return $this->userInterface->getUsers();
    }

    public function getUser()
    {
        return $this->userInterface->getUser();
    }

    public function updateUserAccount (Request $request)
    {
        return $this->userInterface->updateUserAccount($request);
    }

    public function deleteUserAccount ()
    {
        return $this->userInterface->deleteUserAccount();
    }

}
