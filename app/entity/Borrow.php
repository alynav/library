<?php

require_once 'Entity.php';

class Borrow extends Entity
{
    /**
     * @var int
     */
    private $id;

    //data cand a fost imprumut
    /** @var DateTime */
    private $dateCheckedOut;

    //data de returnare
    /** @var DateTime */
    private $dateReturned;

    /**
     * @var boolean
     */
    private $returned = false;

    /**
     * @var Person
     */
    private $person;

    /**
     * @var Book
     */
    private $book;

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id) {
            $borrow = $this->findById($id);
            if (!empty($borrow)) {
                $this->id = $borrow['id'];
                $this->dateCheckedOut = new DateTime($borrow['date_checked_out']);
                $this->dateReturned = new DateTime($borrow['date_returned']);
                $this->returned = $borrow['returned'];

                $person = new Person($borrow['person_id']);
                $this->person = $person;

                $book = new Book($borrow['book_id']);
                $this->book = $book;
            }
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getDateCheckedOut()
    {
        return $this->dateCheckedOut;
    }

    public function setDateCheckedOut($dateCheckedOut): self
    {
        $this->dateCheckedOut = $dateCheckedOut;
        return $this;
    }

    public function getDateReturned()
    {
        return $this->dateReturned;
    }

    public function setDateReturned($dateReturned): self
    {
        $this->dateReturned = $dateReturned;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReturned(): bool
    {
        return $this->returned;
    }

    public function setReturned($returned)
    {
        $this->returned = $returned;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson(Person $person)
    {
        $this->person = $person;
    }

    public function getBook()
    {
        return $this->book;
    }

    public function setBook(Book $book)
    {
        $this->book = $book;
    }

    public function save()
    {
        /** @var DateTime $dateCheckedOut */
        $dateCheckedOut = $this->dateCheckedOut;
        $dateCheckedOut = $dateCheckedOut->format('Y-m-d H:i:s');
        /** @var DateTime $dateReturned */
        $dateReturned = $this->dateReturned;
        $dateReturned = $dateReturned->format('Y-m-d H:i:s');

        $returned = (int)$this->returned;
        $personId = (int)$this->person->getId();
        $bookId = (int)$this->book->getId();
        $query = "INSERT INTO borrow (`date_checked_out`, `date_returned`, `returned`, `person_id`, `book_id`) 
                    VALUES ('$dateCheckedOut', '$dateReturned', '$returned', '$personId', '$bookId')";

        $id = $this->dbConnection->insert($query);

        if ($id > 0) {
            $this->id = $id;
        }

        return $id;
    }

    public function update()
    {
        if ($this->id == null) {
            return false;
        }
        $dateReturned = $this->dateReturned->format('Y-m-d H:i:s');
        $bookId = (int)$this->book->getId();
        $personId = (int)$this->person->getId();
        $returned = (int)$this->returned;

        $query = "UPDATE borrow 
        SET `person_id` = '$personId', `book_id` = '$bookId', `date_returned` = '$dateReturned', `returned` = '$returned'
                    WHERE `id` = " . (int)$this->id;

        return $this->dbConnection->query($query);
    }

    public function delete()
    {
        if ($this->id == null) {
            return false;
        }
        $query = "DELETE FROM borrow WHERE `id` = " . (int)$this->id;
        return $this->dbConnection->query($query);
    }

    public function isValid()
    {
        return !empty($this->person) && !empty($this->book) && !empty($this->dateReturned);
    }

    public function findById(int $id): array
    {
        $query = "SELECT * FROM borrow WHERE `id` = " . (int)$id;
        $result = $this->dbConnection->query($query);

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return [];
    }

    public function findAll(bool $returned = null, bool $late = null): array
    {
        $borrows = [];
        $query = "SELECT * FROM borrow";

        if ($returned !== null){
            $query .= " WHERE `returned` = " . (int)$returned;

            if ($late !== null){
                $query .= " AND DATEDIFF(CURDATE(), `date_returned`) > 0";
            }
        }

        $result = $this->dbConnection->query($query);
        if ($result->num_rows > 0) {
            $borrows = $result->fetch_all(MYSQLI_ASSOC);
        }
        foreach ($borrows as $id => $borrow){
            $query = "SELECT * FROM persons WHERE `id` = " . $borrow['person_id'];
            $result = $this->dbConnection->query($query);
            if ($result->num_rows == 1){
                $person = $result->fetch_assoc();

                $borrows[$id]['person'] = $person;
            }

            $query = "SELECT * FROM books WHERE `id` = " . $borrow['book_id'];
            $result = $this->dbConnection->query($query);
            if ($result->num_rows == 1){
                $book = $result->fetch_assoc();

                $borrows[$id]['book'] = $book;
            }
        }
        return $borrows;
    }
}