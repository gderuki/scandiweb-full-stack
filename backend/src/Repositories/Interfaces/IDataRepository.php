<?php

namespace Repositories\Interfaces;

interface IDataRepository extends IBaseRepository
{
    /**
     * Retrieves single entity by identifier.
     *
     * @param mixed $id The identifier of the entity to retrieve.
     * @return mixed An entity object if found, null otherwise.
     */
    public function get($id);

    /**
     * Retrieves all entities in the repository.
     *
     * @return array An array of entity objects.
     */
    public function getAll();
}