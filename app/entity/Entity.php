<?php

require_once 'Database.php';

abstract class Entity
{
    /**
     * @var Database
     */
    protected $dbConnection;

    public function __construct()
    {
        $this->dbConnection = Database::getInstance();
    }

    abstract public function save();

    abstract public function update();

    abstract public function delete();

    abstract public function isValid();

    abstract public function findById(int $id): array;

    abstract public function findAll(): array;
}