<?php
require_once __DIR__ . '/../entity/Borrow.php';

class BorrowController
{
    public function createAction($dateReturned, $personId, $bookId): bool
    {
        $borrow = new Borrow();
        $borrow->setDateCheckedOut(new DateTime());
        $borrow->setDateReturned(new DateTime($dateReturned));
        $borrow->setReturned(false);

        $person = new Person($personId);
        $borrow->setPerson($person);

        $book = new Book($bookId);
        $borrow->setBook($book);

        if ($borrow->isValid()) {
            return $borrow->save();
        }

        return false;
    }

    public function updateAction($id, $personId, $bookId, $dateReturned, $returned = false)
    {
        $borrow = new Borrow($id);
        if ($borrow->getId()){
            $person = new Person($personId);
            $borrow->setPerson($person);
            $book = new Book($bookId);
            $borrow->setBook($book);

            $borrow->setDateReturned(new DateTime($dateReturned));
            $borrow->setReturned($returned);

            if ($borrow->isValid()){
                return $borrow->update();
            }
            return false;
        }
    }

    public function readAction($id = null)
    {
        $borrow = new Borrow();
        if ($id){
            return $borrow->findById($id);
        } else {
            return $borrow->findAll();
        }
    }

    public function deleteAction($id)
    {
        $borrow = new Borrow($id);
        if ($borrow->getId()){
            return $borrow->delete();
        }
        return false;
    }

    public function getAllNotReturnedAction(bool $late = false)
    {
        $borrow = new Borrow();
        if ($late == true){
            return $borrow->findAll(false, true);
        }
        return $borrow->findAll(false);
    }
}