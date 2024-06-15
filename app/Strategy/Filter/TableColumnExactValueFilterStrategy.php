<?php

namespace App\Strategy\Filter;

class TableColumnExactValueFilterStrategy implements FilterStrategy
{
    public function apply($query, $table, $column, $values)
    {
        if (in_array('null', $values)) {

            return $query->where(function ($q) use ($table, $column, $values) {
                $q->whereIn($table . '.' . $column, array_filter($values, fn ($value) => $value !== 'null'))
                    ->orWhereNull($table . '.' . $column);
            });
        } else {
            return $query->whereIn($table . '.' . $column, $values);
        }
    }
}
