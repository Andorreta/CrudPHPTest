<?php namespace CrudTest;

class CrudTest
{
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
     * Create the CRUD table into the default database
     *
     * @return bool Operation result
     */
    public function createDatabaseTable()
    {
        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code and return it's data //
        $success = $connector->query('CREATE TABLE crud (code VARCHAR, name VARCHAR, ' .
            'description VARCHAR DEFAULT NULL, PRIMARY KEY (code))') or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $success;
    }

    /**
     * Insert a new document into the database (Batch or single document insertion)
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
        if (!$this->hasRequiredData($data) && is_array($data[0])) {
            foreach ($data as $product) {
                if ($this->hasRequiredData($product)) {
                    $connector->query('INSERT INTO crud (code, name, description) VALUES ("' . $product['code'] .
                        '", "' . $product['name'] . '", "' . $product['description'] . '")') or
                    die(mysqli_error($connector));
                }
            }
        } elseif ($this->hasRequiredData($data)) {
            $connector->query('INSERT INTO crud (code, name, description) VALUES ("' . $data['code'] . '", "' .
                $data['name'] . '", "' . $data['description'] . '")') or die(mysqli_error($connector));
        } else {
            $hasNotError = false;
        }

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $hasNotError;
    }

    /**
     * Get the desired value from the Database
     *
     * @param string $code Code String to look in the Database
     * @return array|false False if Error
     */
    public function readDatabaseEntry($code)
    {
        if (empty($code) || !$this->codeIsValid($code)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code //
        $output = $connector->query('SELECT * FROM crud WHERE code="' . $code . '"') or die(mysqli_error($connector));
        $output = $output->fetch_assoc();

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $output;
    }

    /**
     * Update a row of the Database
     *
     * @param string $code Code String to look in the Database
     * @param array $data Data to insert into de Database
     * @return bool|mysqli_result
     */
    public function updateDatabaseEntry($code, array $data)
    {
        if (empty($code) || !$this->codeIsValid($code) || empty($data) || !is_array($data)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Create the SET part of the query //
        $sqlQuerySet = [];
        if (!empty($data['code'])) {
            $sqlQuerySet[] = 'code="' . $data['code'] . '"';
        }
        if (!empty($data['name'])) {
            $sqlQuerySet[] = 'name="' . $data['name'] . '"';
        }
        if (!empty($data['description'])) {
            $sqlQuerySet[] = 'description="' . $data['description'] . '"';
        }
        $sqlQuerySet = trim(implode(count($sqlQuerySet) > 1 ? ',' : ' ', $sqlQuerySet), ', ');

        // Search the database for the code and update it's data //
        $output = $connector->query('UPDATE crud SET ' . $sqlQuerySet . ' WHERE code="' .
            $code . '"') or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $output;
    }

    /**
     * Delete the desired value row from the Database
     *
     * @param string $code Code String to look in the Database
     * @return bool
     */
    public function deleteDatabaseEntry($code)
    {
        if (empty($code) || !$this->codeIsValid($code)) {
            return false;
        }

        // Check if we have a connection to the database
        $connector = $this->connectToDatabase();

        // Search the database for the code and delete it //
        $success = $connector->query('DELETE "' . $code .'" FROM crud') or die(mysqli_error($connector));

        // Close the Database connection
        $this->closeDatabaseConnection($connector);

        return $success;
    }

    /**
     * Check if the array has the needed row values (Code = primary Key, Name = Required and Description = Can be NULL)
     *
     * @param array $data Data array
     * @return bool
     */
    protected function hasRequiredData(array $data)
    {
        return !empty($data['code']) && !empty($data['name']) && isset($data['description']);
    }

    /**
     * Check if the code value is valid (avoid SQL injection)
     *
     * @param string $code Code string value (Text code, example: 123FSDA45-67)
     * @return bool
     */
    protected function codeIsValid($code)
    {
        $result = preg_match('#[\s|\"|\']+#smi', $code);

        return empty($result);
    }
}