<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'task_id' => $task->id,
            'content' => $request->content,
        ]);

        ActivityLog::record(
            'comment.created',
            $comment,
            "Comment added to task \"{$task->title}\" by {$request->user()->name}."
        );

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}
