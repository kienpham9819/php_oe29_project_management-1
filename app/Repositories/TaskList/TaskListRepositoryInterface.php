<?php
namespace App\Repositories\TaskList;

interface TaskListRepositoryInterface
{
    /*
     * Get existed tasks in a tasklist
     *
     */
    public function tasks($id);
}
