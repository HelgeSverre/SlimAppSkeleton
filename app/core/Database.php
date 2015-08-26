<?php

namespace Helge\Framework;

/**
 * Class Database
 * Responsible for handling database interaction and abstraction.
 * @extends PDO
 */
class Database extends \PDO
{

    /**
     * @param string $host the database server hostname
     * @param string $name database name
     * @param string $user database user
     * @param string $password database password
     * @param string $charset database charset
     */
    public function __construct($host, $name, $user, $password, $charset = "utf8")
    {
        // Build the data source name string.
        $dsn = "mysql:host=" . $host . ";dbname=" . $name . ";charset=" . $charset;

        // Initialize the PDO object that we're extending with our dsn string
        parent::__construct($dsn, $user, $password);

        // Make PDO return associative arrays by default.
        $this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }


    /**
     * Fetches all rows from $table
     * @param string $table table name to return all the rows for.
     * @return  array of all rows in $table
     */
    public function fetchAll($table)
    {
        $stmt = $this->query("SELECT * FROM {$table}");
        return $stmt->fetchAll();
    }


    /**
     * @param string $table the table to fetch data from in the database
     * @param string $field the field to test against $value
     * @param string $value the the value to look for
     * @return mixed row of data or false on failure
     */
    public function fetchAllWhere($table, $field, $value)
    {

        $stmt = $this->prepare("SELECT * FROM {$table} WHERE {$field}=:value");
        $stmt->execute(array("value" => $value));
        $result = $stmt->fetch();
        return $result;
    }


    /**
     * @param string $table the table to fetch data from
     * @param int $id the number of the field ID to check for
     * @return mixed row data on success, false on failure.
     */
    public function fetchAllWhereId($table, $id)
    {
        $stmt = $this->prepare("SELECT * FROM {$table} WHERE id=:id");
        $stmt->execute(array(":id" => $id));
        $result = $stmt->fetch();

        return $result;
    }


    /**
     * builds up an SQL statement AND executes it from an associative array and inserts it into $table
     * @param string $table table to insert data into
     * @param array $data associated array of data to be inserted into $table
     * @return int rows affected.
     * @TODO: refactor using prepared statements
     */
    public function insert($table, $data)
    {

        // adds quotes to the value fields, this is an sql thing,
        // needs refactoring later, it works for now
        $unescapedValues = array_values($data);

        foreach ($unescapedValues as &$value) {
            $value = $this->quote($value);
        }

        // This will implode the data array keys and values as well as
        // insert a comma, it will also strip of the comma on the right side
        $values = rtrim(implode(array_values($unescapedValues), ', '), ', ');
        $keys = rtrim(implode(array_keys($data), ', '), ', ');

        // Build SQL string
        $sql = "INSERT INTO {$table}( {$keys} ) VALUES( {$values} )";

        // Execute our query
        $rowsAffected = $this->exec($sql) or die(print_r($this->errorInfo(), true));

        // Should only return 1 or more if successful or 0 if it fails.
        return $rowsAffected;
    }


    /**
     * @param string $table te table to delete a row from
     * @param int $id the id of the row to delete
     * @return int amount of rows affected, if 0 the query failed.
     */
    public function deleteWhereId($table, $id)
    {
        $stmt = $this->prepare("DELETE FROM {$table} WHERE id = :id");
        $stmt->execute(array(":id" => $id));
        $result = $stmt->rowCount();

        return $result;
    }

}