<?php

namespace Services\Interfaces;

/**
 * Provides access to data in collections and single instances.
 */
interface IDataAccess
{
    public function getAll();
    public function get($id);
}