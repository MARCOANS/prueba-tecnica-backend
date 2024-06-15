<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Department::factory(500)->create()->each(function ($department) {
            if (rand(0, 1)) {
                $parent = Department::inRandomOrder()->first();
                $department->parent_id = $parent->id;
                $department->save();
            }
        });
    }
}
