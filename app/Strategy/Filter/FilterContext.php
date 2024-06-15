<?php

namespace App\Strategy\Filter;

use App\Strategy\Filter\FilterStrategy;

class FilterContext
{
    protected $strategy;

    public function setStrategy(FilterStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function applyFilter($query, $table, $column, $values)
    {
        return $this->strategy->apply($query, $table, $column, $values);
    }
}
