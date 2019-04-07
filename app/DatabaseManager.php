<?php namespace CrudTest;

abstract class DatabaseManager
{

    /**
     * @var string Class table name
     */
    protected $table = '';


    /**
     * Returns the SQL Query to create the required table
     *
     * @return string
     */
    abstract protected function generateTableCreationQuery();

    /**
     * Returns the SQL Query to insert data into the database
     *
     * @param array $data Data array
     * @return string
     */
    abstract protected function generateDatabaseInsertionQuery(array $data);

    /**
     * Returns the SQL Query to get the desired data from the database
     *
     * @param mixed $id Key String
     * @return string
     */
    abstract protected function generateDatabaseSelectQuery($id);

    /**
     * Returns the SQL Query to update some data from the database
     *
     * @param string $id Key String
     * @param array $data Data array
     * @return string
     */
    abstract protected function generateDatabaseUpdateQuery($id, $data);

    /**
     * Check if the array has the needed table Row values
     *
     * @param array $data Data array
     * @return bool
     */
    abstract protected function dataHasRequiredValues(array $data);

    /**
     * Create a connector for the Database
     *
     * @return mysqli
     */
    protected function connectToDatabase()
    {
        $mysql = new mysqli('localhost', 'mysql', '123456', 'indra_crud_test') or die(mysqli_error($mysql));

        return $mysql;
    }

    /**
     * Create a connector for the Database
     *
     * @param mysqli $connection Database connection
     */
    protected function closeDatabaseConnection(mysqli $connection)
    {
        $connection->close();
    }

    /**
     * Create the Products CRUD table into the default database
     *
     * @return bool Operation result
     */
    public function createDatabaseTable()
    {
        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code and return it's data //
        $success = $connector->query($this->generateTableCreationQuery()) or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return !empty($success);
    }

    /**
     * Insert a new document into the desired Database (Batch or single document insertion)
     *
     * @param array $data Data to insert into de Database
     * @return bool True if success
     */
    public function createDatabaseEntry(array $data)
    {
        $hasNotError = true;

        if (empty($data) || !is_array($data)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Insert the data into the Database //
        if (!$this->dataHasRequiredValues($data) && is_array($data[0])) {
            foreach ($data as $dataRow) {
                if ($this->dataHasRequiredValues($dataRow)) {
                    $connector->query($this->generateDatabaseInsertionQuery($dataRow)) or die(mysqli_error($connector));
                }
            }
        } elseif ($this->dataHasRequiredValues($data)) {
            $connector->query($this->generateDatabaseInsertionQuery($data)) or die(mysqli_error($connector));
        } else {
            $hasNotError = false;
        }

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $hasNotError;
    }

    /**
     * Get the desired value from the Products Database
     *
     * @param string $id String Key to look in the Database
     * @return array|false False if Error
     */
    public function readDatabaseEntry($id)
    {
        if (empty($id) || !$this->keyIdIsValid($id)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code //
        $output = $connector->query($this->generateDatabaseSelectQuery($id)) or die(mysqli_error($connector));
        $output = empty($output) ? false : $output->fetch_assoc();

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $output;
    }

    /**
     * Update a row of the Products Database
     *
     * @param string $id String Key to look in the Database
     * @param array $data Data to insert into de Database
     * @return bool|mysqli_result
     */
    public function updateDatabaseEntry($id, array $data)
    {
        if (empty($id) || !$this->keyIdIsValid($id) || empty($data) || !is_array($data)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code and update it's data //
        $output = $connector->query($this->generateDatabaseUpdateQuery($id, $data)) or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return !empty($output);
    }

    /**
     * Delete the desired value row from the desired Database
     *
     * @param string $code Code String to look in the Database
     * @return bool
     */
    public function deleteDatabaseEntry($code)
    {
        if (empty($code) || !$this->keyIdIsValid($code)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code and delete it //
        $success = $connector->query($this->generateDatabaseDeleteQuery($code)) or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $success;
    }

    /**
     * Returns the SQL Query to delete some data from the database
     *
     * @param string $id Key String
     * @return string
     */
    protected function generateDatabaseDeleteQuery($id)
    {
        return 'DELETE "' . $id .'" FROM ' . $this->table;
    }

    /**
     * Check if the product Code value is valid (avoid SQL injection)
     *
     * @param string $id Code string value (Text code, example: 123FSDA45-67)
     * @return bool
     */
    protected function keyIdIsValid($id)
    {
        $result = preg_match('#[\s|\"|\']+#smi', $id);

        return empty($result);
    }
}