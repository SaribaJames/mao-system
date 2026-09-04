<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        // "all" shows every type together and is the default view. Anything
        // unrecognised in the URL falls back to "all" rather than erroring.
        $type = request('type', 'all');
        if (!in_array($type, ['all', 'announcement', 'training', 'event'], true)) {
            $type = 'all';
        }

        $query = Activity::with('createdBy')->where('status', 'active');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        // withQueryString() keeps ?type= on the pagination links, otherwise
        // page 2 silently drops back to the default view.
        $activities = $query->latest()->paginate(10)->withQueryString();

        $announcements = Activity::where('type', 'announcement')->where('status', 'active')->count();
        $trainings     = Activity::where('type', 'training')->where('status', 'active')->count();
        $events        = Activity::where('type', 'event')->where('status', 'active')->count();
        $total         = $announcements + $trainings + $events;

        return view('activities.index', compact(
            'activities', 'type',
            'announcements', 'trainings', 'events', 'total'
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