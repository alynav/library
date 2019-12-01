<?php

require_once 'Entity.php';

class Person extends Entity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var boolean
     */
    private $isAuthor = 0;

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id){
            $person = $this->findById($id);

            if (!empty($person)){
                $this->id = $person['id'];
                $this->firstName = $person['firstname'];
                $this->lastName = $person['lastname'];
                $this->isAuthor = $person['is_author'];
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAuthor(): bool
    {
        return $this->isAuthor;
    }

    /**
     * @param bool $isAuthor
     * @return $this
     */
    public function setIsAuthor(bool $isAuthor): self
    {
        $this->isAuthor = $isAuthor;
        return $this;
    }

    public function save()
    {
        $firstname = $this->dbConnection->escapeString($this->firstName);
        $lastname = $this->dbConnection->escapeString($this->lastName);
        $isAuthor = (int) $this->isAuthor;
        $query = "INSERT INTO persons (`firstname`, `lastname`, `is_author`) 
                    VALUES ('$firstname', '$lastname', '$isAuthor')";

        $id = $this->dbConnection->insert($query);

        if ($id > 0){
            $this->id = $id;
        }

        return $id;
    }

    public function update()
    {
        if ($this->id == null){
            return false;
        }
        $firstname = $this->dbConnection->escapeString($this->firstName);
        $lastname = $this->dbConnection->escapeString($this->lastName);
        $isAuthor = (int)$this->isAuthor;
        $query = "UPDATE persons SET 
                        `firstname` = '$firstname', `lastname` = '$lastname', `is_author` = '$isAuthor' 
                        WHERE `id` = " . (int)$this->id;

        return $this->dbConnection->query($query);
    }

    public function delete()
    {
        if ($this->id == null){
            return false;
        }
        $query = "DELETE FROM persons WHERE `id` = " . (int)$this->id;
        return $this->dbConnection->query($query);
    }

    public function isValid()
    {
        return !empty($this->firstName) && !empty($this->lastName);
    }

    public function findById($id): array
    {
        $query = "SELECT * FROM persons WHERE `id` = " . (int)$id;
        $result = $this->dbConnection->query($query);
        if ($result->num_rows == 1){
           return $result->fetch_assoc();
        }
        return [];
    }

    public function findAll(?bool $isAuthor = null): array
    {
        $query = "SELECT * FROM persons";

        if ($isAuthor == true){
            $query .= " WHERE `is_author` = 1";
        }

        $result = $this->dbConnection->query($query);
        if ($result->num_rows > 0){
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}