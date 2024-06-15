<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DepartmentService
{

    protected $filterableColumns = [
        'name' => 'simple',
        'level' => 'simple',
        'parent_name' => ['type' => 'relation', 'relation' => 'parent', 'field' => 'name']
    ];

    protected $searchableColumns = [
        'name' => ['type' => 'simple'],
        'parent_name' => ['type' => 'relation', 'relation' => 'parent', 'field' => 'name'],
        'employees' => ['type' => 'simple'],
        'level' => ['type' => 'simple'],
        'ambassador' => ['type' => 'simple'],
        'children_count' => ['type' => 'count']
    ];

    public function applyFilters($query, $filters)
    {
        foreach ($filters as $key => $values) {
            if (isset($this->filterableColumns[$key])) {
                $config = $this->filterableColumns[$key];

                if ($config === 'simple') {
                    $query->whereIn('departments.' . $key, $values);
                } elseif (is_array($config) && $config['type'] === 'relation') {
                    $relation = $config['relation'];
                    $field = $config['field'];
                    if (in_array('null', $values)) {
                        $query->where(function ($q) use ($relation, $field, $values) {
                            $q->whereNull('departments.' . $relation . '_id')
                                ->orWhereHas($relation, function ($q) use ($field, $values) {
                                    $q->whereIn('parent_departments.' . $field, $values);
                                });
                        });
                    } else {
                        $query->whereHas($relation, function ($q) use ($values, $field) {
                            $q->whereIn('parent_departments.' . $field, $values);
                        });
                    }
                }
            }
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

        $searchString = 'CONCAT(departments.name," ",parent_departments.name," ",departments.employees," ",departments.level," ",departments.ambassador) like ?';



        if (isset($searchTerm)) {

            if ($columnSearch === 'all') {
                $searchString = 'CONCAT(departments.name, " ", parent_departments.name, " ", departments.employees, " ", departments.level) LIKE ?';
                $query->whereRaw($searchString, ["%{$searchTerm}%"]);
            } elseif (isset($this->searchableColumns[$columnSearch])) {
                $columnConfig = $this->searchableColumns[$columnSearch];
                if ($columnConfig['type'] === 'simple') {
                    if ($searchTerm === '-') {
                        $query->whereNull("departments.{$columnSearch}");
                    } else {
                        $query->where("departments.{$columnSearch}", 'like', "%{$searchTerm}%");
                    }
                } elseif ($columnConfig['type'] === 'relation') {
                    if ($searchTerm === '-') {

                        $query->whereDoesntHave($columnConfig['relation']);
                    } else {
                        $query->whereRelation($columnConfig['relation'], $columnConfig['field'], 'like', "%{$searchTerm}%");
                    }
                } elseif ($columnConfig['type'] === 'count') {
                    if ($searchTerm === '-') {
                        $query->having($columnSearch, '=', 0);
                    } else {
                        $query->having($columnSearch, '=', $searchTerm);
                    }
                }
            }
        }


        if (isset($sortColumn, $sortOrder))
            $query->orderBy($sortColumn, $sortOrder);


        $query = $this->applyFilters($query, $filters);


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
            $department->delete();
            return response()->json(['message' => 'Department deleted successfully'], 200);
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
