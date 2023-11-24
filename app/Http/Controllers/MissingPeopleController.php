<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\MissingPeopleInterface;
use Illuminate\Http\Request;

class MissingPeopleController extends Controller
{
    public $missingPeopleInterface;
    public function __construct (MissingPeopleInterface $missingPeopleInterface)
    {
        $this->missingPeopleInterface = $missingPeopleInterface;
    }

    public function addMissingPerson (request $request)
    {
        return $this->missingPeopleInterface->addMissingPerson($request);
    }

    public function getMissingPeople ()
    {
        return $this->missingPeopleInterface->getMissingPeople();
    }

    public function updateMissingPersonInformation (request $request)
    {
        return $this->missingPeopleInterface->updateMissingPersonInformation($request);
    }

    public function deleteMissingPersonInformation ($missingPersonId)
    {
        return $this->missingPeopleInterface->deleteMissingPersonInformation($missingPersonId);
    }
}
