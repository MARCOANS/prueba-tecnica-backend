<?php

declare(strict_types=1);

namespace App\Services;

use App\Factory\FilterStrategyFactory;
use App\Models\Department;

use App\Strategy\Filter\FilterContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DepartmentService
{

    protected $filterableColumns = [
        'name' => ['type' => 'table_column_exact_value', 'table' => 'departments', 'column' => 'name'],
        'level' => ['type' => 'table_column_exact_value', 'table' => 'departments', 'column' => 'level'],
        'parent_name' => ['type' => 'table_column_exact_value', 'table' => 'parent_departments', 'column' => 'name'],
    ];

    protected $searchableColumns = [
        'name' => ['type' => 'table_column_partial_match', 'table' => 'departments', 'column' => 'name'],
        'parent_name' => ['type' => 'table_column_partial_match', 'table' => 'parent_departments', 'column' => 'name'],
        'employees' => ['type' => 'table_column_partial_match', 'table' => 'departments', 'column' => 'employees'],
        'level' => ['type' => 'table_column_partial_match', 'table' => 'departments', 'column' => 'level'],
        'ambassador' => ['type' => 'table_column_partial_match', 'table' => 'departments', 'column' => 'ambassador'],
        'children_count' => ['type' => 'table_column_count_value', 'table' => null, 'column' => 'children_count'],
    ];

    public function applyExactFilters($query, $filters)
    {

        $filterContext = new FilterContext();

        foreach ($filters as $column => $values) {

            if (isset($this->filterableColumns[$column])) {
                $config = $this->filterableColumns[$column];
                $strategy = FilterStrategyFactory::create($config['type'], $config);
                $filterContext->setStrategy($strategy);
                $filterContext->applyFilter($query, $config['table'], $config['column'], $values);
            }
        }

        return $query;
    }


    public function applySearch($query, $searchTerm, $columnSearch)
    {
        $filterContext = new FilterContext();

        if ($columnSearch === 'all') {

            $searchString = 'CONCAT(departments.name," ",parent_departments.name," ",departments.employees," ",departments.level," ",departments.ambassador) like ?';

            $query->whereRaw($searchString, ["%{$searchTerm}%"]);
        } elseif (isset($this->searchableColumns[$columnSearch])) {
            $config = $this->searchableColumns[$columnSearch];
            $strategy = FilterStrategyFactory::create($config['type'], $config);
            $filterContext->setStrategy($strategy);
            $filterContext->applyFilter($query, $config['table'], $config['column'], $searchTerm);
        }

        return $query;
    }
    public function columnsFilters()
    {

        $departments = Department::distinct()->orderBy('name')->pluck('name');
        $parents = Department::whereHas('children')->distinct()->orderBy('name')->pluck('name');
        $levels = Department::distinct()->orderBy('level')->pluck('level');

        return ['departments' => $departments, 'parents' => $parents, 'levels' => $levels];
    }
    function dataTable($request)
    {
        $perPage = $request->perPage ?? 10;
        $searchTerm = $request->term;
        $sortOrder = data_get($request->sort, 'order');
        $sortColumn = data_get($request->sort, 'column');
        $filters = $request->filters ?? [];
        $columnSearch = $request->columnSearch ?? 'all';

        $query = Department::select(
            'departments.*',
            'parent_departments.name as parent_name'
        )
            ->leftJoin('departments as parent_departments', 'departments.parent_id', '=', 'parent_departments.id')
            ->withCount(['children']);


        if (isset($searchTerm)) {
            $query = $this->applySearch($query, $searchTerm, $columnSearch);
        }


        if (isset($sortColumn, $sortOrder))
            $query->orderBy($sortColumn, $sortOrder);


        $query = $this->applyExactFilters($query, $filters);


        return $query->paginate($perPage);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    function show($id): Department
    {
        return Department::findOrFail($id)->load(['parent', 'children']);
    }

    function update(Department $record, array $data): Department
    {
        $record->update($data);

        return $record;
    }

    function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);
            return $department->delete();
        } catch (ModelNotFoundException $e) {
            throw new HttpException(404, "Department not found");
        } catch (\Throwable $th) {
            throw new HttpException(500, "Unable to delete the department: " . $th->getMessage());
        }
    }

    public function getSubDepartments(Department $department)
    {
        return $department->children;
    }
}
