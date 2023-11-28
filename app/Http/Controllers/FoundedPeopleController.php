<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\FoundedPeopleInterface;
use Illuminate\Http\Request;

class FoundedPeopleController extends Controller
{
    public $foundedPeopleInterface;
    public function __construct (FoundedPeopleInterface $foundedPeopleInterface)
    {
        $this->foundedPeopleInterface = $foundedPeopleInterface;
    }

    public function addFoundedPerson (request $request)
    {
        return $this->foundedPeopleInterface->addFoundedPerson($request);
    }

    public function getFoundedPeople ()
    {
        return $this->foundedPeopleInterface->getFoundedPeople();
    }

    public function updateFoundedPersonData (request $request)
    {
        return $this->foundedPeopleInterface->updateFoundedPersonData($request);
    }

    public function deleteFoundedPersonData ($foundedPersonId)
    {
        return $this->foundedPeopleInterface->deleteFoundedPersonData($foundedPersonId);
    }
}
