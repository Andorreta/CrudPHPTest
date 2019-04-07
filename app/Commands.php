<?php namespace CrudTest;

// Get the JSON from the web server //
if (isset($_POST['json-upload'])) {
    $crudTest = new CrudTest();
    return $crudTest->createDatabaseEntry($_POST['json-upload']);
}
// if needed, add the rest of the Web engine calls (For Read, Update and Delete)

// Get the JSON from the command line shell arguments //
if (isset($argc) && $argc >= 2) {
    switch ($argv[1]) {
        case 'create':
            if ($argc !== 3) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[2]) ? json_decode(file_get_contents($argv[2]), true) : $argv[2];

            $crudTest = new CrudTest();
            return $crudTest->createDatabaseEntry($data);

        case 'get':
        case 'read':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new CrudTest();
            return $crudTest->readDatabaseEntry($argv[2]);

        case 'update':
            if ($argc !== 4) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[3]) ? json_decode(file_get_contents($argv[3]), true) : $argv[3];

            $crudTest = new CrudTest();
            return $crudTest->updateDatabaseEntry($argv[2], $data);

        case 'delete':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new CrudTest();
            return $crudTest->deleteDatabaseEntry($argv[2]);

        default:
            // Show a help message //
            echo 'The usage is "php CrudTest.php <function> <data>"';
            echo '   Where function can be "create", "read", "update" and "delete"';
            echo '   And data can be a JSON file path or a JSON formatted string';
            return false;
    }
}

return false;