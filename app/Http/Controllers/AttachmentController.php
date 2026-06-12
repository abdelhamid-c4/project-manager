<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'attachable_type' => 'required|in:App\Models\Project,App\Models\Task',
            'attachable_id' => 'required|integer',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = Attachment::create([
            'user_id' => $request->user()->id,
            'attachable_type' => $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function show(Attachment $attachment): StreamedResponse
    {
        $this->authorize('view', $attachment);

        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->response(
            $attachment->file_path,
            $attachment->file_name,
            ['Content-Type' => $attachment->file_type]
        );
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}
