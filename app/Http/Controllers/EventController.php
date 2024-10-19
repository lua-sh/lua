<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use App\Models\User;
use App\Models\LinkStat;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        $start = Carbon::createFromFormat('Y-m-d', $request->start ? $request->start : now()->subDays(30)->format('Y-m-d'))->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $request->end ? $request->end : now()->format('Y-m-d'))->endOfDay();

        $query = LinkStat::where('workspace_id', $workspace->id)
            ->with('link:id,link')
            ->latest();

        // search
        if ($request->q) {
            $query->where(function ($query) use ($request) {
                // $query->where('name', 'LIKE', '%' . $request->q . '%');
                // $query->orWhere('code', 'LIKE', '%' . $request->q . '%');
                // $query->orWhereHas('products', function ($query) use ($request) {
                //     $query->where('name', 'LIKE', '%' . $request->q . '%');
                // });
            });
        }

        $links = $query->paginate(config('app.pagination.default'));


        return Inertia::render('Event/Index', [
            'table' => $links,
            'hasData' => LinkStat::where('workspace_id', $workspace->id)->exists(),
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);
    }
}