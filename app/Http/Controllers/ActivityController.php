<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $type          = request('type', 'announcement');
        $activities    = Activity::with('createdBy')
                            ->where('type', $type)
                            ->where('status', 'active')
                            ->latest()
                            ->paginate(10);

        $announcements = Activity::where('type', 'announcement')->where('status', 'active')->count();
        $trainings     = Activity::where('type', 'training')->where('status', 'active')->count();
        $events        = Activity::where('type', 'event')->where('status', 'active')->count();

        return view('activities.index', compact(
            'activities', 'type',
            'announcements', 'trainings', 'events'
        ));
    }

public function store(Request $request)
{
    $request->validate([
        'type'       => 'required|in:announcement,training,event',
        'title'      => 'required|string|max:200',
        'body'       => 'required|string',
        'priority'   => 'required|in:high,normal,low',
        'event_date' => 'nullable|date',
        'location'   => 'nullable|string|max:200',
    ]);

    Activity::create([
        'type'       => $request->type,
        'title'      => $request->title,
        'content'    => $request->input('body'),
        'priority'   => $request->priority,
        'event_date' => $request->event_date,
        'location'   => $request->location,
        'status'     => 'active',
        'created_by' => Auth::id(),
    ]);

    return redirect()->route('activities.index', ['type' => $request->type])
        ->with('success', 'Posted successfully!');
}
        

    public function destroy(Activity $activity)
    {
        $type = $activity->type;
        $activity->delete();
        return redirect()->route('activities.index', ['type' => $type])
            ->with('success', 'Deleted successfully!');
    }
}