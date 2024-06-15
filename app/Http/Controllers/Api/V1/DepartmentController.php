<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function __construct(private  DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return new JsonResponse($this->departmentService->dataTable($request));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request)
    {
        $record = $this->departmentService->create($request->validated());

        return new JsonResponse(['message' => 'Departamento creado', 'record' => $record]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new JsonResponse($this->departmentService->show($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $record = $this->departmentService->update($department, $request->validated());

        return new JsonResponse(['message' => 'Departamento actualizado', 'record' => $record]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->departmentService->destroy($id);

        return new JsonResponse(['message' => 'Departamento eliminado']);
    }

    public function columnsFilters()
    {
        return new JsonResponse($this->departmentService->columnsFilters());
    }

    public function subDepartments(Department $department)
    {
        return new JsonResponse($this->departmentService->getSubDepartments($department));
    }
}
