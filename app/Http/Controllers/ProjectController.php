<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Project;
use App\Mail\ProjectSubmit;
use App\Http\Requests\ProjectRequest;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectLinkRequest;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Http\Requests\GradeRequest;
use Exception;
use App\Mail\NotiGrade;
use App\Jobs\SendWarning;
use App\Jobs\FinishProject;
use App\Notifications\ProjectApproved;
use App\Events\NotifyEvent;
use Pusher\Pusher;

class ProjectController extends Controller
{
    protected $projectRepository, $userRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, UserRepositoryInterface $userRepository)
    {
        $this->middleware('auth');
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function create(Group $group)
    {
        $group->load('course');

        return view('projects.create', compact(['group']));
    }

    public function index()
    {
        $user = auth()->user();
        $courses = $user->courses()->orderBy('updated_at', 'desc')->limit(config('app.display_limit'))->get();
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');
        $projects = $this->projectRepository->projectsFromGroups($groups, config('paginate.record_number'));

        return view('projects.index', compact([
            'courses',
            'projects',
        ]));
    }

    public function store(ProjectRequest $request, Group $group)
    {
        $this->projectRepository->create([
            'name' => $request->name,
            'description' => $request->description,
            'group_id' => $group->id,
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show($project, Request $request)
    {
        $project = $this->projectRepository->find($project, ['group.users', 'taskLists', 'tasks']);
        $this->authorize('view', $project);
        $completed = $this->projectRepository->completedTask($project->id);
        if ($completed === false) {
            abort(404);
        }
        $unfinished = $this->projectRepository->unfinishedTask($project->id);
        if ($unfinished === false) {
            abort(404);
        }
        if ($this->userRepository->hasRole('student')) {
            $repositories = $this->userRepository
                ->getGithubRepositories(
                    auth()->user()->id,
                    $request->header('User-Agent')
                );

            return view('projects.show', compact(['project', 'unfinished', 'completed', 'repositories']));
        } elseif ($this->userRepository->hasRole('admin')) {
            return view('users.admin.project_detail', compact(['project', 'unfinished', 'completed']));
        }

        return view('users.lecturer.project_detail', compact(['project', 'unfinished', 'completed']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($project)
    {
        $project = $this->projectRepository->find($project, ['group.users.roles', 'taskLists']);
        $this->authorize('update', $project);
        if (auth()->user()->hasRole('student')) {
            return view('projects.edit', compact('project'));
        }

        return view('users.admin.project_edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, $project)
    {
        $project = $this->projectRepository->find($project);
        $this->authorize('update', $project);
        $this->projectRepository->update($project->id, [
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.show', [$project->id]);
    }


    public function linkGithubRepository(ProjectLinkRequest $request, $project)
    {
        $project = $this->projectRepository->find($project);
        $this->authorize('update', $project);
        $result = $this->projectRepository->update($project->id, [
            'git_repository' => $request->git_repository,
        ]);

        return redirect()->route('projects.show', [$project->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy($project)
    {
        $project = $this->projectRepository->find($project);
        $this->authorize('delete', $project);
        $this->projectRepository->delete($project->id);
        if (auth()->user()->hasRole('student')) {
            return redirect()->route('projects.index');
        }

        return redirect()->route('groups.show', $project->group->id);
    }

    public function toggle($project)
    {
        if (!$this->projectRepository->toggle($project)) {
            abort(404);
        }
        $project = $this->projectRepository->find($project, ['group.users']);
        if ($project->is_accepted) {
            foreach ($project->group->users as $user) {
                $notification = new ProjectApproved($project->name, route('projects.show', [$project->id]));
                $user->notify($notification);
                broadcast(new NotifyEvent([
                    'channel' => $user->id,
                    'notifications' => $this->userRepository->getNotifications($user->id),
                ]))->toOthers();
            }
        }

        return back();
    }

    public function submit($projectId)
    {
        $project = $this->projectRepository->update($projectId, [
            'is_completed' => true,
        ]);
        if (!$project) {
            abort(404);
        }
        $lecturer = $this->userRepository->getLecturer($project);
        $emailJob = new FinishProject($project, $lecturer);
        dispatch($emailJob);

        return redirect()->route('projects.show', [$projectId]);
    }

    public function grade(GradeRequest $request, $projectId)
    {
        $data = [
            'grade' => $request->grade,
            'review' => $request->review,
        ];
        $project = $this->projectRepository->update($projectId, $data);
        if (!$project) {
            abort(404);
        }
        $users = $this->projectRepository->getMember($projectId);
        $emailJob = new SendWarning($data, $users);
        dispatch($emailJob);

        return redirect()->route('projects.show', [$projectId]);
    }
}
