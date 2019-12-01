<?php

require_once __DIR__ . '/../entity/Book.php';
require_once __DIR__ . '/../entity/Person.php';

class BookController
{
    /**
     * @param string $title
     * @param string $isbn
     * @param int $quantity
     * @param array $authorsId
     * @return Book
     */
    public function createAction(string $title, string $isbn, int $quantity, array $authorsId)
    {
        $book = new Book();
        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setQuatity($quantity);

        if ($book->isValid()) {
            $book->save();
            //adaug autorii
            if ($book->getId()) {
                foreach ($authorsId as $authorId) {
                    $person = new Person($authorId);
                    if ($person->getIsAuthor() == true) {
                        $book->saveAuthor($person);
                    }
                }
            }
        }
        return $book;
    }

    public function updateAction(int $id, string $title, string $isbn, int $quantity, array $authorsId)
    {
        $book = new Book($id);

        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setQuatity($quantity);

        $book->clearAuthors();

        foreach ($authorsId as $idAuthor) {
            if (!array_key_exists($idAuthor, $book->getAuthors())) {
                $person = new Person($idAuthor);
                $book->addAuthor($person);
            }
        }

        if ($book->isValid()) {
            return $book->update();
        }
        return false;

    }

    public function readAction($id = null)
    {
        $book = new Book();
        if ($id) {
            return $book->findById($id);
        } else {
            return $book->findAll();
        }
    }

    public function deleteAction($id)
    {
        $book = new Book($id);
        if ($book->getId()) {
            return $book->delete();
        }
        return false;
    }

    public function searchAction(string $search)
    {
        $book = new Book();
        return $book->findAll($search);
    }
}