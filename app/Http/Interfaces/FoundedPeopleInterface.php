<?php
namespace App\Http\Interfaces;

interface FoundedPeopleInterface {

    public function addFoundedPerson($request);
    public function getFoundedPeople();
    public function updateFoundedPersonData($request);
    public function deleteFoundedPersonData($foundedPersonId);
}
?>