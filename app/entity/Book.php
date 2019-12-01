<?php

require_once 'Entity.php';

class Book extends Entity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var array
     */
    private $authors = []; //key => val; 'id' => Person

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id) {
            $book = $this->findById($id);

            if (!empty($book)) {
                $this->id = $book['id'];
                $this->title = $book['title'];
                $this->isbn = $book['isbn'];
                $this->quantity = $book['quantity'];
                $this->setAuthors();
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

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     * @return $this
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuatity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }


    public function save()
    {
        $title = $this->dbConnection->escapeString($this->title);
        $isbn = $this->dbConnection->escapeString($this->isbn);
        $quantity = (int)$this->quantity;

        $query = "INSERT INTO books (`title`, `isbn`, `quantity`) 
                    VALUES ('$title', '$isbn', '$quantity')";

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
        $title = $this->dbConnection->escapeString($this->title);
        $isbn = $this->dbConnection->escapeString($this->isbn);
        $quantity = (int)$this->quantity;
        $query = "UPDATE books SET `title` = '$title', `isbn` = '$isbn', `quantity` = '$quantity' 
                    WHERE `id` = " . (int)$this->id;

        $bookUpdate = $this->dbConnection->query($query);

        if ($bookUpdate) {
            foreach ($this->authors as $author) {
                $this->saveAuthor($author);
            }
        }
        return $bookUpdate;
    }

    public function clearAuthors(){
        $this->authors = [];

        $sql = "DELETE FROM book_authors WHERE `book_id` = " . (int)$this->id;
        return $this->dbConnection->query($sql);
    }

    public function delete()
    {
        $bookQuery = "DELETE FROM books WHERE `id` = " . (int)$this->id;
        $resultBook = $this->dbConnection->query($bookQuery);

        $clearAuthors = $this->clearAuthors();

        return $resultBook && $clearAuthors;
    }

    public function isValid()
    {
        return !empty($this->title) && !empty($this->isbn) && !empty($this->quantity);
    }

    public function findById(int $id): array
    {
        $query = "SELECT * FROM books WHERE id = " . (int)$id;
        $result = $this->dbConnection->query($query);
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return [];
    }

    public function findAll(string $search = ''): array
    {
        $books = [];

        $query = "SELECT * FROM books";

        if (!empty($search)){
            $search = $this->dbConnection->escapeString($search);
            $query .= " WHERE `title` LIKE '%" . $search . "%'";
        }
        $result = $this->dbConnection->query($query);
        if ($result->num_rows > 0) {
            $books = $result->fetch_all(MYSQLI_ASSOC);
        }

        foreach ($books as $id => $book){
            $authors = [];
            $query = "SELECT * FROM book_authors WHERE `book_id` = " . $book['id'];
            $temp = $this->dbConnection->query($query);
            if ($temp->num_rows > 0){
                $authors = $temp->fetch_all(MYSQLI_ASSOC);
            }
            $books[$id]['authors'] = $authors;
        }
        return $books;
    }

    public function saveAuthor(Person $person)
    {
        if (!isset($this->authors[$person->getId()])) {
            $this->authors[$person->getId()] = $person;
        }
        $personId = $person->getId();
        $query = "INSERT INTO book_authors (`book_id`, `person_id`) VALUES ('$this->id', '$personId')";

        return $this->dbConnection->query($query);
    }

    private function setAuthors()
    {
        if ($this->id) {
            $query = "SELECT `person_id` FROM book_authors WHERE `book_id` = " . $this->id;
            $result = $this->dbConnection->query($query);
            $authorsId = $result->fetch_all(MYSQLI_ASSOC);

            $authors = [];
            foreach ($authorsId as $authorId) {
                $authors[] = new Person($authorId);
            }
            $this->authors = $authors;
        }
    }

    public function getAuthors()
    {
        return $this->authors;
    }

    public function addAuthor(Person $person)
    {
        if ($person->getIsAuthor() && !array_key_exists($person->getId(), $this->authors)) {
            $this->authors[$person->getId()] = $person;
        }
    }
}