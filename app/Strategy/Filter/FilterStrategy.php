<?php

namespace App\Strategy\Filter;

interface FilterStrategy
{
    public function apply($query, $table, $column, $values);
}

