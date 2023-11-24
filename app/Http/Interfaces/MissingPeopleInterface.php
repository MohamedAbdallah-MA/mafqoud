<?php
namespace App\Http\Interfaces;

interface MissingPeopleInterface {

    public function addMissingPerson($request);
    public function getMissingPeople();
    public function updateMissingPersonInformation($request);
    public function deleteMissingPersonInformation($missingPersonId);
}
?>