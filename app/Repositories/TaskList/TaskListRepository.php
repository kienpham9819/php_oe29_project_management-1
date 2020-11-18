<?php
namespace App\Repositories\TaskList;

use App\Repositories\BaseRepository;
use App\Models\TaskList;
use DB;

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

    public function activities($id)
    {
        $taskList = $this->find($id);
        if ($taskList) {
            $activities = $taskList->tasks()
                ->where('is_completed', true)
                ->orderBy('tasks.updated_at', 'desc')
                ->select(DB::raw('count(tasks.updated_at) as activities, date(`tasks`.`updated_at`) as date'))
                ->groupBy('date')
                ->get();

            return $activities;
        }

        return [];
    }

    public function completedTask($id)
    {
        $tasks = $this->tasks($id);
        if ($tasks) {
            $completed = $tasks->where('is_completed', true)->count();

            return $completed;
        }

        return false;
    }

    public function unfinishedTask($id)
    {
        $tasks = $this->tasks($id);
        if ($tasks) {
            $unfinished = $tasks->where('is_completed', false)->count();

            return $unfinished;
        }

        return false;
    }
}
