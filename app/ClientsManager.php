<?php namespace CrudTest;

class ClientsManager extends DatabaseManager
{

    /**
     * @var string Class table name
     */
    protected $table = 'clients';

    /**
     * {@inheritDoc}
     *
     * Note: The productsList key is a JSON formatted string of all the related product codes
     */
    protected function generateTableCreationQuery()
    {
        return 'CREATE TABLE ' . $this->table . ' (dni VARCHAR, firstname VARCHAR, lastname VARCHAR,' .
            ' address VARCHAR, phone VARCHAR, email VARCHAR, productsList VARCHAR DEFAULT "[]", PRIMARY KEY (dni))';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseInsertionQuery(array $data)
    {
        return 'INSERT INTO ' . $this->table .' (dni, firstname, lastname, address, phone, email, productsList) ' .
            'VALUES ("' . $data['dni'] . '", "' . $data['firstname'] . '", "' . $data['lastname'] . '", "' .
            $data['address'] . '", "' . $data['phone'] . '", "' . $data['email'] . '", "")';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseSelectQuery($id)
    {
        return 'SELECT * FROM ' . $this->table . ' WHERE dni="' . $id . '"';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseUpdateQuery($id, $data)
    {
        // Create the SET part of the query //
        $sqlQuerySet = [];
        if (!empty($data['dni'])) {
            $sqlQuerySet[] = 'dni="' . $data['dni'] . '"';
        }
        if (!empty($data['firstname'])) {
            $sqlQuerySet[] = 'firstname="' . $data['firstname'] . '"';
        }
        if (!empty($data['lastname'])) {
            $sqlQuerySet[] = 'lastname="' . $data['lastname'] . '"';
        }
        if (!empty($data['address'])) {
            $sqlQuerySet[] = 'address="' . $data['address'] . '"';
        }
        if (!empty($data['phone'])) {
            $sqlQuerySet[] = 'phone="' . $data['phone'] . '"';
        }
        if (!empty($data['email'])) {
            $sqlQuerySet[] = 'email="' . $data['email'] . '"';
        }
        if (!empty($data['productsList'])) {
            $sqlQuerySet[] = 'productsList="' . $data['productsList'] . '"';
        }
        $sqlQuerySet = trim(implode(count($sqlQuerySet) > 1 ? ',' : ' ', $sqlQuerySet), ', ');

        return 'UPDATE ' . $this->table . ' SET ' . $sqlQuerySet . ' WHERE dni="' . $id . '"';
    }

    /**
     * {@inheritDoc}
     */
    protected function dataHasRequiredValues(array $data)
    {
        $requiredKeysList = ['dni', 'firstname', 'lastname', 'address', 'phone', 'email', 'productsList'];

        return empty(array_diff($requiredKeysList, array_keys($data)));
    }

    /**
     * Gets all the product codes from a client
     *
     * @param string $clientDni Client DNI
     * @return array|false
     */
    public function readProductsFromClient($clientDni)
    {
        // Get the Client Associated products //
        $clientData = $this->readDatabaseEntry($clientDni);

        return json_decode($clientData['productsList'], true);
    }

    /**
     * Adds a new product to a Client
     *
     * @param string $clientDni Client DNI
     * @param string $productId Product Code
     * @return bool False if Error
     */
    public function addProductToClient($clientDni, $productId)
    {
        // Get the Client Associated products //
        $productsList = (array)$this->readProductsFromClient($clientDni);

        // Add the new product ID //
        if (empty($productsList) || !in_array($productId, $productsList)) {
            $productsList[] = $productId;
        }

        // Update the Client Data with the new product //
        return !empty($this->updateDatabaseEntry($clientDni, ['productsList' => json_encode($productsList)]));
    }

    /**
     * Removes new product Code from the Client
     *
     * @param string $clientDni Client DNI
     * @param string $productId Product Code
     * @return bool False if Error
     */
    public function deleteProductFromClient($clientDni, $productId)
    {
        // Get the Client Associated products //
        $productsList = (array)$this->readProductsFromClient($clientDni);

        // Remove the desired product ID //
        if (!empty($productsList) && in_array($productId, $productsList)) {
            if (($key = array_search($productId, $productsList)) !== false) {
                unset($productsList[$key]);
            }
        }

        // Update the Client Data //
        return !empty($this->updateDatabaseEntry($clientDni, ['productsList' => json_encode($productsList)]));
    }

    /**
     * Modifies a product Code From the Client
     *
     * @param string $clientDni Client DNI
     * @param string $oldProductId Old Product Code
     * @param string $newProductId New Product Code
     * @return bool False if Error
     */
    public function updateProductFromClient($clientDni, $oldProductId, $newProductId)
    {
        $this->deleteProductFromClient($clientDni, $oldProductId);

        return !empty($this->addProductToClient($clientDni, $newProductId));
    }
}