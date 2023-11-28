<?php
namespace App\Http\Interfaces;

interface MissingPeopleInterface {

    public function addMissingPerson($request);
    public function getMissingPeople();
    public function updateMissingPersonData($request);
    public function deleteMissingPersonData($missingPersonId);
}
?>