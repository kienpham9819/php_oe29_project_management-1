<?php

namespace App\Repositories\Project;

use App\Repositories\RepositoryInterface;

interface ProjectRepositoryInterface extends RepositoryInterface
{
    public function completedTask($id);

    public function unfinishedTask($id);

    public function toggle($id);

    public function projectsFromGroups($groups = [], $paginate = 0);

    public function getLastestProject($groups);

    public function getMember($projectId);
}
