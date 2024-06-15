<?php

namespace App\Strategy\Filter;

use Illuminate\Support\Facades\Log;

class TableColumnPartialMatchFilterStrategy implements FilterStrategy
{
    public function apply($query, $table, $column, $term)
    {
        if ($term === '-') {

            return $query->whereNull($table . '.' . $column);
        } else {
            return $query->where($table . '.' . $column, 'like', '%' . $term . '%');
        }
    }
}
