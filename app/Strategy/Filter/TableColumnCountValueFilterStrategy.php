<?php

namespace App\Strategy\Filter;

class TableColumnCountValueFilterStrategy implements FilterStrategy
{
    public function apply($query, $table, $column, $term)
    {
        $countValue = intval($term);

        return $query->having($column, '=', $countValue);
    }
}
