<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TasksStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tasks' => 'required',
            'tasks.*.name' => 'string',
            'tasks.*.task_list_id' => 'required|exists:task_lists,id',
            'tasks.*.is_completed' => 'boolean',
        ];
    }
}
