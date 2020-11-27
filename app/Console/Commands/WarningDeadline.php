<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\TaskList\TaskListRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Carbon\Carbon;
use App\Notifications\Warning;
use Pusher\Pusher;
use App\Events\NotificationEvent;

class WarningDeadline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning:deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for user to warning deadline';
    protected $taskListRepository;
    protected $userRepository;
    protected $projectRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        TaskListRepositoryInterface $taskListRepository,
        UserRepositoryInterface $userRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        parent::__construct();
        $this->taskListRepository = $taskListRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tasklists = $this->taskListRepository->getAll();
        $now = Carbon::now();
        foreach ($tasklists as $tasklist) {
            $deadline = $tasklist->due_date->subDay(config('mail.warning_days'));
            $user = $this->userRepository->find($tasklist->user_id);
            $project = $this->projectRepository->find($tasklist->project_id);
            $leader = $this->userRepository->getLeader($project->group_id);
            if ($deadline <= $now && $now <= $tasklist->due_date) {
                $data = [
                    'tasklistName' => $tasklist->name,
                    'url' => route('projects.task-lists.show', [$project->id, $tasklist->id]),
                    'projectName' => $project->name,
                    'user_id' => $tasklist->user_id,
                ];
                $user->notify(new Warning($data));
                // $options = array(
                //     'cluster' => 'ap1',
                //     'encrypted' => true
                // );

                // $pusher = new Pusher(
                //     env('PUSHER_APP_KEY'),
                //     env('PUSHER_APP_SECRET'),
                //     env('PUSHER_APP_ID'),
                //     $options
                // );
                // $pusher->trigger('NotificationEvent', 'warning-message', $data);

                event(new NotificationEvent($data));
            }
        }
    }
}
