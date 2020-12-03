@extends('users.lecturer.index')

@section('breadcrumb')
    <li class="breadcrumb-item active text-capitalize" aria-current="page">
       {{ trans('course.title_list') }}
    </li>
@endsection

@section('user')
    <div id="user-list">
        <label class="h3">
            {{ trans('course.list') }}
        </label>
        <table class="table table-hover table-bordered mb-4 mt-3">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ trans('general.name') }}</th>
                    <th scope="col" class="text-center">{{ trans('general.details') }}</th>
                </tr>
            </thead>
            <tbody id="table">
                @isset ($courses)
                    @forelse ($courses as $key => $course)
                        <tr>
                            <td scope="row"> {{ ++$key }} </td>
                            <td>
                                <label class="font-weight-bold">
                                    {{ $course->name }}
                                </label>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('lecturers.courseDetail', $course->id) }}">
                                    <i class="fas fa-info-circle text-info"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <p>{{ trans('general.empty', ['attribute' => trans('course.course')]) }}</p>
                    @endforelse
                @else
                    <p>{{ trans('general.empty', ['attribute' => trans('course.course')]) }}</p>
                @endisset
            </tbody>
        </table>
        {{ $courses->links() }}
    </div>
    <input type="number" id="k">
    <div id="view_data">a</div>
    <canvas id="line-chart"></canvas>
@endsection
