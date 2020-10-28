<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            [
                'name' => 'create user',
                'slug' => 'create-user',
            ],
            [
                'name' => 'update user',
                'slug' => 'update-user',
            ],
            [
                'name' => 'delete user',
                'slug' => 'delete-user',
            ],
            [
                'name' => 'view user',
                'slug' => 'view-user',
            ],
            [
                'name' => 'create class',
                'slug' => 'create-class',
            ],
            [
                'name' => 'update class',
                'slug' => 'update-class',
            ],
            [
                'name' => 'delete class',
                'slug' => 'delete-class',
            ],
            [
                'name' => 'view class',
                'slug' => 'view-class',
            ],
            [
                'name' => 'create group',
                'slug' => 'create-group',
            ],
            [
                'name' => 'update group',
                'slug' => 'update-group',
            ],
            [
                'name' => 'delete group',
                'slug' => 'delete-group',
            ],
            [
                'name' => 'view group',
                'slug' => 'view-group',
            ],
             [
                'name' => 'create project',
                'slug' => 'create-project',
            ],
            [
                'name' => 'update project',
                'slug' => 'update-project',
            ],
            [
                'name' => 'delete project',
                'slug' => 'delete-project',
            ],
            [
                'name' => 'view project',
                'slug' => 'view-project',
            ],
            [
                'name' => 'accept project',
                'slug' => 'accept-project',
            ],
             [
                'name' => 'create tasklist',
                'slug' => 'create-tasklist',
            ],
            [
                'name' => 'update tasklist',
                'slug' => 'update-tasklist',
            ],
            [
                'name' => 'delete tasklist',
                'slug' => 'delete-tasklist',
            ],
            [
                'name' => 'update task progress',
                'slug' => 'update-task-progress',
            ],
        ]);
    }
}
