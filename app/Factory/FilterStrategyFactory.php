<?php

namespace App\Factory;

use App\Strategy\Filter\TableColumnCountValueFilterStrategy;
use App\Strategy\Filter\TableColumnExactValueFilterStrategy;
use App\Strategy\Filter\TableColumnPartialMatchFilterStrategy;

class FilterStrategyFactory
{
    public static function create($type, $config = null)
    {
        switch ($type) {
            case 'table_column_exact_value':
                return new TableColumnExactValueFilterStrategy();
            case 'table_column_partial_match':
                return new TableColumnPartialMatchFilterStrategy();
            case 'table_column_count_value':
                return new TableColumnCountValueFilterStrategy();

            default:
                throw new \InvalidArgumentException("Unknown filter type: $type");
        }
    }
}
