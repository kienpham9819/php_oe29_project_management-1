<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\Rule;

class CoursesImport implements  WithValidation, WithHeadingRow,ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $data = $emails = $names = array();
        foreach ($rows as $row) {
            $emails[] = $row['email'];
            $names[] = $row['name'];
        }
        $userIds = User::select('id', 'email')->whereIn('email', $emails)->get();
        foreach ($emails as $key => $email) {
            $data[] = [
                'name' =>  $names[$key],
                'user_id' => $userIds->where('email', $email)->first()->id,
            ];
        }
        Course::insert($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:courses',
            'email' => [
                'required',
                'email',
                Rule::exists('users')->where(function ($query) {
                    $query->whereIn('id', Role::findOrFail(config('admin.lecturer'))->users()->pluck('users.id'));
                }),
            ],
        ];
    }
}
