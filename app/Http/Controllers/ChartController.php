<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use DB;

class ChartController extends Controller
{
    public function showChart($input)
    {
        $data = Task::select('task_list_id', DB::raw('sum(is_completed) as total'))->groupBy('task_list_id')->whereIn('id', [$input, $input+1])->get()->toJson();

        return $data;
    }
}
