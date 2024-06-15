<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'name' => 'required|string|max:45|unique:departments,name,' . $departmentId,
            'parent_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1',
            'employees' => 'required|integer|min:1',
            'ambassador' => 'nullable|string|max:255',
        ];
    }
}
