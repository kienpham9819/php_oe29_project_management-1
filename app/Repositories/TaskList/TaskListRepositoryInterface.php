<?php
namespace App\Repositories\TaskList;

use App\Repositories\RepositoryInterface;

interface TaskListRepositoryInterface extends RepositoryInterface
{
    /*
     * Get existed tasks in a tasklist
     *
     */
    public function tasks($id);

    public function activities($id);

    public function completedTask($id);

    public function unfinishedTask($id);
}
