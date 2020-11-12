<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Group;
use App\Models\Course;
use DB;

class EditGroupRequest extends FormRequest
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
        $groupId = (int)explode("/", $this::url())[4];
        $GroupCurrent = Group::findOrFail($groupId);
        $nameGroupCurrent = $GroupCurrent->name;
        $courseId = $GroupCurrent->course_id;
        $groupNames = DB::table('groups')->select('name')->where('course_id', $courseId)->get()->toArray();
        $data = json_decode(json_encode($groupNames), true);
        $nameGroupInvalids = array();
        $key = 0;
        foreach ($data as $value) {
            if ($value['name'] == $nameGroupCurrent) continue;
            $nameGroupInvalids[$key] = $value['name'];
            $key++;
        }

        return [
            'name_group' => [
                'required',
                'string',
                'min:1',
                'max:50',
                Rule::notIn($nameGroupInvalids),
            ],
        ];
    }
}
