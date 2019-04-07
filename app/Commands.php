<?php namespace CrudTest;

// Get the JSON from the web server //
if (isset($_POST['json-upload'])) {
    $crudTest = new ProductsManager();
    return $crudTest->createDatabaseEntry($_POST['json-upload']);
}
// if needed, add the rest of the Web engine calls (For Read, Update and Delete)

// Get the JSON from the command line shell arguments //
if (isset($argc) && $argc >= 2) {
    switch ($argv[1]) {
        case 'createProduct':
        case 'createProducts':
            if ($argc !== 3) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[2]) ? json_decode(file_get_contents($argv[2]), true) : $argv[2];

            $crudTest = new ProductsManager();
            return $crudTest->createDatabaseEntry($data);

        case 'getProduct':
        case 'readProduct':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new ProductsManager();
            return $crudTest->readDatabaseEntry($argv[2]);

        case 'updateProduct':
            if ($argc !== 4) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[3]) ? json_decode(file_get_contents($argv[3]), true) : $argv[3];

            $crudTest = new ProductsManager();
            return $crudTest->updateDatabaseEntry($argv[2], $data);

        case 'deleteProduct':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new ProductsManager();
            return $crudTest->deleteDatabaseEntry($argv[2]);

        case 'createClient':
        case 'createClients':
            if ($argc !== 3) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[2]) ? json_decode(file_get_contents($argv[2]), true) : $argv[2];

            $crudTest = new ClientsManager();
            return $crudTest->createDatabaseEntry($data);

        case 'getClient':
        case 'readClient':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->readDatabaseEntry($argv[2]);

        case 'updateClient':
            if ($argc !== 4) {
                return false;
            }

            // Get the data if it's inside a JSON File //
            $data = file_exists($argv[3]) ? json_decode(file_get_contents($argv[3]), true) : $argv[3];

            $crudTest = new ClientsManager();
            return $crudTest->updateDatabaseEntry($argv[2], $data);

        case 'deleteClient':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->deleteDatabaseEntry($argv[2]);

        case 'linkProduct':
            if ($argc !== 4) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->addProductToClient($argv[2], $argv[3]);

        case 'unlinkProduct':
            if ($argc !== 4) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->deleteProductFromClient($argv[2], $argv[3]);

        case 'getClientProducts':
            if ($argc !== 3) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->readProductsFromClient($argv[2]);

        case 'updateClientProduct':
            if ($argc !== 5) {
                return false;
            }

            $crudTest = new ClientsManager();
            return $crudTest->updateProductFromClient($argv[2], $argv[3], $argv[4]);

        default:
            // Show a help message //
            echo 'The usage is "php CrudTest.php <function> <data> [additional args]"';
            echo '   Where function can be "createProduct", "getProduct", "updateProduct" and "deleteProduct" for products';
            echo '   and for clients can be "createClient", "getClient", "updateClient" and "deleteClient"';
            echo '   For linking a products to a clients its "linkProduct", "unlinkProduct", "getClientProducts" and "updateClientProduct"';
            return false;
    }
}

return false;