<?php


namespace Helge\Framework;


/**
 * Authentication class that uses role based permissions, the role
 * is pretty much just an integer, we use this class to authenticate
 * the user if their role(integer) is equal or higher than a required role
 * Class Authentication
 * @package Abax\Worker
 */
class Authentication
{

    /**
     * @var \PDO database connection
     */
    protected $db;

    /**
     * @var string The column in the database that is used as the user id
     */
    protected $userIdColumn;

    /**
     * @var string the table in the database used to store users
     */
    protected $userTable;

    /**
     * @var string the column in the database used to store a user's role
     */
    protected $roleColumn;


    public function __construct(\PDO $db, $userIdColumn = "id", $userTable = "users", $roleColumn = "role")
    {

        $this->db = $db;

        $this->userIdColumn = $userIdColumn;;
        $this->userTable = $userTable;;
        $this->roleColumn = $roleColumn;;
    }


    /**
     * Checks if the user has a sufficiently high role
     * @param int $userId user id
     * @param int $role the role to check if the user with id of $userId matches
     * @return bool true if user has high enough role, false if not
     */
    public function authForRole($userId, $role)
    {
        $userRole = $this->getUserRole($userId);
        return ($userRole >= $role);
    }

    /**
     * Checks if the user has a sufficiently high role
     * @param int $userId user id
     * @param int $role the role to check if the user with id of $userId matches exactly
     * @return bool true if user has high enough role, false if not
     */
    public function authForExactRole($userId, $role)
    {
        $userRole = $this->getUserRole($userId);
        return ($userRole == $role);
    }


    /**
     * Gets the role that a user has
     * @param int $userId the userId
     * @return mixed the role of the user
     */
    public function getUserRole($userId)
    {

        $stmt = $this->db->prepare("SELECT $this->roleColumn FROM $this->userTable WHERE id = :userid");
        $stmt->execute(array("userid" => $userId));

        $userRole = $stmt->fetch(\PDO::FETCH_COLUMN);

        return $userRole;
    }


}





























