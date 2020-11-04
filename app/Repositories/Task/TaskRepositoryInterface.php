<?php
namespace App\Repositories\Task;

interface TaskRepositoryInterface
{
    /*
     * Insert multiple tasks
     */
    public function insert($task);

    /*
     * Toggle is_completed attribute of a task
     */
    public function toggle($id);
}
