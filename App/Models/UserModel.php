<?php

namespace App\Models;

use PDOException;
use PDO;
use Aura\SqlQuery\QueryFactory;

class UserModel
{
    private $pdo, $queryFactory;

    public function __construct(PDO $pdo, QueryFactory $queryFactory)
    {
        try {
            $this -> pdo = $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $this->queryFactory = $queryFactory;
    }

    /**
     * getUser() method return user information for ID
     *
     * @param integer $userId
     * @return object User information
     */
    public function getUser(int $userId): object
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("users");
        $select->join(
            'LEFT',             // the join-type
            'users_info AS info',        // join to this table ...
            'users.id = info.user_id' // ... ON these conditions
        );
        $select->join(
            'LEFT',             // the join-type
            'users_images AS image',        // join to this table ...
            'image.id = info.image_id' // ... ON these conditions
        );
        $select->where("users.id = {$userId}");
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute();
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    /**
     * getUserForEmail() method return user information for email
     *
     * @param string $userEmail
     * @return object User information
     */
    public function getUserForEmail(string $userEmail): object
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("users");
        $select->join(
            'LEFT',             // the join-type
            'users_info AS info',        // join to this table ...
            'users.id = info.user_id' // ... ON these conditions
        );
        $select->join(
            'LEFT',             // the join-type
            'users_images AS image',        // join to this table ...
            'image.id = info.image_id' // ... ON these conditions
        );
        $select->where("users.email = {$userEmail}");
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute();
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    /**
     * getUsers() method return all users 
     *
     * @return array all users
     */
    public function getUsers(): array
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from("users");
        $select->join(
            'LEFT',             // the join-type
            'users_info AS info',        // join to this table ...
            'users.id = info.user_id' // ... ON these conditions
        );
        $select->join(
            'LEFT',             // the join-type
            'users_images AS image',        // join to this table ...
            'image.id = info.image_id' // ... ON these conditions
        );
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * update() method update data in database
     *
     * @param string $table
     * @param string $where
     * @param string|integer $id
     * @param array $params
     * @return boolean
     */
    public function update(string $table, string $where, string|int $id, array $params): bool
    {
        $update = $this->queryFactory->newUpdate();
        $update->table("{$table}")->cols($params)->where("{$where} = {$id}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());
    }

    /**
     * updateMainTable() method update data in "users" table
     *
     * @param string|integer $id
     * @param array $params
     * @return boolean
     */
    public function updateMainTable(string|int $id, array $params): bool
    {
        $update = $this->queryFactory->newUpdate();
        $update->table("users")->cols($params)->where("id = {$id}")->bindValues($params);
        $sth = $this->pdo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());
    }

    /**
     * insert() method insert new data in database
     *
     * @param string $table
     * @param array $params
     * @return boolean
     */
    public function insert(string $table, array $params): bool
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into("$table")->cols($params);
        $sth = $this->pdo->prepare($insert->getStatement());
        return $sth->execute($insert->getBindValues());
    }

    /**
     * delete() method delete data in database
     *
     * @param string $table
     * @param integer $id
     * @return boolean
     */
    public function delete(string $table, int $id): bool
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from("{$table}")->where("user_id = {$id}");
        $sth = $this->pdo->prepare($delete->getStatement());
        return $sth->execute();
    }

    /**
     * deleteDataForUser() method delete user data in database
     *
     * @param integer $userId
     * @return boolean
     */
    public function deleteDataForUser(int $userId): bool
    {
        return $this->delete('users_info', $userId);
    }
}