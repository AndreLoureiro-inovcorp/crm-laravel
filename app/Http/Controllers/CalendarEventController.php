<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Deal;
use App\Models\Entity;
use App\Models\Person;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource (Calendar View).
     */
    public function index(Request $request)
    {
        $events = CalendarEvent::with(['eventable', 'owner'])
            ->when($request->start, fn($q, $start) => $q->where('start_at', '>=', $start))
            ->when($request->end, fn($q, $end) => $q->where('end_at', '<=', $end))
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_at->toIso8601String(),
                    'end' => $event->end_at->toIso8601String(),
                    'description' => $event->description,
                    'location' => $event->location,
                    'owner' => $event->owner?->name,
                    'eventable_type' => class_basename($event->eventable_type ?? ''),
                    'eventable_name' => $event->eventable?->name ?? $event->eventable?->title ?? null,
                ];
            });

        return Inertia::render('Calendar/Index', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $entities = Entity::select('id', 'name')->orderBy('name')->get();
        $people = Person::select('id', 'name')->orderBy('name')->get();
        $deals = Deal::select('id', 'title as name')->orderBy('title')->get();

        return Inertia::render('Calendar/Create', [
            'entities' => $entities,
            'people' => $people,
            'deals' => $deals,
            'preselected' => [
                'type' => $request->eventable_type,
                'id' => $request->eventable_id,
                'start' => $request->start,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eventable_type' => 'nullable|in:Entity,Person,Deal',
            'eventable_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['owner_id'] = auth()->id();

        // Convert eventable_type to full class name
        if ($validated['eventable_type']) {
            $validated['eventable_type'] = "App\\Models\\{$validated['eventable_type']}";
        }

        CalendarEvent::create($validated);

        return redirect()->route('calendar.index')
            ->with('success', 'Evento criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendar)
    {
        $this->authorize('view', $calendar);

        $calendar->load(['eventable', 'owner']);

        return Inertia::render('Calendar/Show', [
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'description' => $calendar->description,
                'start_at' => $calendar->start_at->toIso8601String(),
                'end_at' => $calendar->end_at->toIso8601String(),
                'location' => $calendar->location,
                'owner' => $calendar->owner,
                'eventable_type' => class_basename($calendar->eventable_type ?? ''),
                'eventable' => $calendar->eventable,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEvent $calendar)
    {
        $this->authorize('update', $calendar);

        $entities = Entity::select('id', 'name')->orderBy('name')->get();
        $people = Person::select('id', 'name')->orderBy('name')->get();
        $deals = Deal::select('id', 'title as name')->orderBy('title')->get();

        return Inertia::render('Calendar/Edit', [
            'event' => [
                'id' => $calendar->id,
                'title' => $calendar->title,
                'description' => $calendar->description,
                'start_at' => $calendar->start_at->format('Y-m-d\TH:i'),
                'end_at' => $calendar->end_at->format('Y-m-d\TH:i'),
                'location' => $calendar->location,
                'eventable_type' => class_basename($calendar->eventable_type ?? ''),
                'eventable_id' => $calendar->eventable_id,
            ],
            'entities' => $entities,
            'people' => $people,
            'deals' => $deals,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendar)
    {
        $this->authorize('update', $calendar);

        $validated = $request->validate([
            'eventable_type' => 'nullable|in:Entity,Person,Deal',
            'eventable_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'location' => 'nullable|string|max:255',
        ]);

        // Convert eventable_type to full class name
        if ($validated['eventable_type']) {
            $validated['eventable_type'] = "App\\Models\\{$validated['eventable_type']}";
        }

        $calendar->update($validated);

        return redirect()->route('calendar.index')
            ->with('success', 'Evento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendar)
    {
        $this->authorize('delete', $calendar);

        $calendar->delete();

        return redirect()->route('calendar.index')
            ->with('success', 'Evento eliminado com sucesso.');
    }
}
