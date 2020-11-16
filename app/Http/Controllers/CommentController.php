<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Repositories\Comment\CommentRepositoryInterface;

class CommentController extends Controller
{
    protected $commentRepository, $taskRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->middleware('auth');
        $this->commentRepository = $commentRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        return $this->commentRepository->find($id);
    }

    public function store($id, CommentRequest $request)
    {
        $this->commentRepository->create([
            'user_id' => auth()->user()->id,
            'task_id' => $id,
            'content' => $request->content,
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, $id)
    {
        $this->commentRepository->update($id, [
            'content' => $request->content,
        ]);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->commentRepository->delete($id);

        return true;
    }
}
