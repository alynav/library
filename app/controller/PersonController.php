<?php

require_once __DIR__ . '/../entity/Person.php';

class PersonController
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param bool $isAuthor
     * @return bool
     */
    public function createAction(string $firstName, string $lastName, bool$isAuthor): bool
    {
       $person = new Person();
       $person->setFirstName($firstName);
       $person->setLastName($lastName);
       $person->setIsAuthor($isAuthor);

       if ($person->isValid()) {
           return $person->save();
       }
       return false;
    }

    public function updateAction(int $id, string $firstname, string $lastname, bool $isAuthor)
    {
        $person = new Person($id);
        if ($person->getId()){
            $person->setFirstName($firstname);
            $person->setLastName($lastname);
            $person->setIsAuthor($isAuthor);

            if ($person->isValid()){
                return $person->update();
            }
            return false;
        }
    }

    public function readAction($id = null)
    {
        $person = new Person();
        if ($id){
            return $person->findById($id);
        } else {
            return $person->findAll();
        }
    }

    public function deleteAction($id)
    {
        $person = new Person($id);
        if ($person->getId()){
            return $person->delete();
        }
        return false;
    }
}