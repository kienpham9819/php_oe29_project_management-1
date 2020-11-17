<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttachmentRequest;
use App\Repositories\Attachment\AttachmentRepositoryInterface;

class AttachmentController extends Controller
{
    protected $attachmentRepository;

    public function __construct(AttachmentRepositoryInterface $attachmentRepository)
    {
        $this->middleware('auth');
        $this->attachmentRepository = $attachmentRepository;
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
                $name = $file->getClientOriginalName();
                $path = $file->store('uploads', 'public');
                $this->attachmentRepository->create([
                    'task_id' => $request->task_id,
                    'url' => $path,
                    'name' => $name,
                ]);
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
    public function destroy($id)
    {
        $this->attachmentRepository->delete($id);

        return back();
    }
}
