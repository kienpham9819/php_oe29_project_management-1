<?php
namespace App\Repositories\TaskList;

use App\Repositories\BaseRepository;
use App\Models\TaskList;

class TaskListRepository extends BaseRepository implements TaskListRepositoryInterface
{
    public function getModel()
    {
        return TaskList::class;
    }

    public function tasks($id)
    {
        $result = $this->find($id);
        if ($result) {
            return $result->tasks;
        }

        return false;
    }
}
