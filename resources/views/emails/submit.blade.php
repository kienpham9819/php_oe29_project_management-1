@component('mail::message')
# Your students have completed their project.

Dear {{ $lecturer->name }}, <br>
Your students has completed their project. <br>
Project name : {{ $project->name }} <br>
Group: {{ $project->group->name }} <br>

@component('mail::button', ['url' => route('projects.show', [$project->id])])
{{ $project->name }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
