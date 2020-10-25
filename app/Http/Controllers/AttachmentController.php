<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Requests\AttachmentRequest;

class AttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttachmentRequest $request)
    {
        if ($request->hasFile('urls')) {
            $files = $request->file('urls');
            foreach ($files as $file) {
                $attachment = new Attachment;
                $name = $file->getClientOriginalName();
                $path = $file->store('uploads', 'public');
                $attachment->task_id = $request->task_id;
                $attachment->url = $path;
                $attachment->name = $name;
                $attachment->save();
            }
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attachment $attachment)
    {
        $attachment->delete();

        return back();
    }
}
