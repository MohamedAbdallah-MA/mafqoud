<?php
namespace App\Http\Interfaces;

interface FoundedPeopleInterface {

    public function addFoundedPerson($request);
    public function getFoundedPeople();
    public function updateFoundedPersonInformation($request);
    public function deleteFoundedPersonInformation($foundedPersonId);
}
?>