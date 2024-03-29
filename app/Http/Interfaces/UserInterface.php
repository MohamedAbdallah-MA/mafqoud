<?php
namespace App\Http\Interfaces;

interface UserInterface {
    public function getUsers();
    public function getAllUsersPostsInRandomOrder();
    public function getUser();
    public function updateUserAccount($request);
    public function deleteUserAccount();
}

?>