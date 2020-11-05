<?php
namespace App\Repositories\Task;

use App\Repositories\BaseRepository;
use App\Models\Task;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function getModel()
    {
        return Task::class;
    }

    public function insert($tasks)
    {
        return $this->model->insert($tasks);
    }

    public function toggle($id)
    {
        $task = $this->find($id);
        if ($task) {
            $task->is_completed = !$task->is_completed;
            $task->save();

            return $task;
        }

        return false;
    }
}
