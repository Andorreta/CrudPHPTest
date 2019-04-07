<?php namespace CrudTest;

class ProductsManager extends DatabaseManager
{

    /**
     * @var string Class table name
     */
    protected $table = 'products';

    /**
     * {@inheritDoc}
     */
    protected function generateTableCreationQuery()
    {
        return 'CREATE TABLE ' . $this->table .
            ' (code VARCHAR, name VARCHAR, description VARCHAR DEFAULT NULL, PRIMARY KEY (code))';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseInsertionQuery(array $data)
    {
        return 'INSERT INTO ' . $this->table .' (code, name, description) VALUES ("' . $data['code'] . '", "' .
            $data['name'] . '", "' . $data['description'] . '")';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseSelectQuery($id)
    {
        return 'SELECT * FROM ' . $this->table . ' WHERE code="' . $id . '"';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateDatabaseUpdateQuery($id, $data)
    {
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

        return 'UPDATE ' . $this->table . ' SET ' . $sqlQuerySet . ' WHERE code="' . $id . '"';
    }

    /**
     * {@inheritDoc}
     */
    protected function dataHasRequiredValues(array $data)
    {
        return !empty($data['code']) && !empty($data['name']) && isset($data['description']);
    }
}