<?php

namespace App\Repositories\Project;

use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Project;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function getModel()
    {
        return Project::class;
    }

    public function completedTask($id)
    {
        $project = $this->find($id);
        if ($project) {
            return $project->tasks()->where('is_completed', true)->count();
        }

        return false;
    }

    public function unfinishedTask($id)
    {
        $project = $this->find($id);
        if ($project) {
            return $project->tasks()->where('is_completed', false)->count();
        }

        return false;
    }

    public function toggle($id)
    {
        $project = $this->find($id);
        if ($project) {
            $project->is_accepted = !$project->is_accepted;
            $project->save();

            return true;
        }

        return false;
    }

    public function projectsFromGroups($groups = [], $paginate = 0)
    {
        if ($paginate) {
            return $this->model->whereIn('group_id', $groups)
                ->orderBy('updated_at', 'desc')
                ->with(['tasks', 'group.course'])
                ->paginate($paginate);
        } else {
            return $this->model->whereIn('group_id', $groups)
                ->orderBy('updated_at', 'desc')
                ->with(['tasks', 'group.course']);
        }
    }

    public function getLastestProject($groups)
    {
        $projects = Project::whereIn('group_id', $groups)
            ->with('group.course')
            ->orderBy('updated_at', 'desc')->get();
        $projects->splice(config('app.display_limit'));

        return $projects;
    }

    public function getMember($projectId)
    {
        $project = $this->find($projectId);
        $users = $project->group->users;

        return $users;
    }
}
