<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Entity;
use App\Models\Person;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DealController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource (Kanban Board).
     */
    public function index(Request $request)
    {
        $stages = DealStage::orderBy('order')->get();

        $dealsByStage = [];
        foreach ($stages as $stage) {
            $dealsByStage[$stage->id] = Deal::with(['entity:id,name', 'person:id,name', 'owner:id,name'])
                ->where('stage', $stage->name)
                ->when($request->owner_id, fn ($q, $owner) => $q->where('owner_id', $owner))
                ->when($request->min_value, fn ($q, $min) => $q->where('value', '>=', $min))
                ->when($request->max_value, fn ($q, $max) => $q->where('value', '<=', $max))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Deals/Index', [
            'stages' => $stages,
            'dealsByStage' => $dealsByStage,
            'filters' => $request->only(['owner_id', 'min_value', 'max_value']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entities = Entity::select('id', 'name')->orderBy('name')->get();
        $people = Person::select('id', 'name', 'entity_id')->orderBy('name')->get();
        $stages = DealStage::orderBy('order')->get();

        return Inertia::render('Deals/Create', [
            'entities' => $entities,
            'people' => $people,
            'stages' => $stages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'person_id' => 'nullable|exists:people,id',
            'title' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'stage' => 'required|string',
            'probability' => 'required|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['owner_id'] = auth()->id();

        Deal::create($validated);

        return redirect()->route('deals.index')
            ->with('success', 'Negócio criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal)
    {
        $this->authorize('view', $deal);

        $deal->load([
            'entity',
            'person',
            'owner',
            'products',
            'activities',
            'emails',
            'proposal',
            'calendarEvents',
        ]);

        return Inertia::render('Deals/Show', [
            'deal' => $deal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deal $deal)
    {
        $this->authorize('update', $deal);

        $entities = Entity::select('id', 'name')->orderBy('name')->get();
        $people = Person::select('id', 'name', 'entity_id')->orderBy('name')->get();
        $stages = DealStage::orderBy('order')->get();

        return Inertia::render('Deals/Edit', [
            'deal' => $deal,
            'entities' => $entities,
            'people' => $people,
            'stages' => $stages,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $validated = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'person_id' => 'nullable|exists:people,id',
            'title' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'stage' => 'required|string',
            'probability' => 'required|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
        ]);

        $deal->update($validated);

        return redirect()->route('deals.index')
            ->with('success', 'Negócio atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return redirect()->route('deals.index')
            ->with('success', 'Negócio eliminado com sucesso.');
    }

    /**
     * Update deal stage (for Kanban drag-and-drop).
     */
    public function updateStage(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $validated = $request->validate([
            'stage' => 'required|string',
        ]);

        $deal->update(['stage' => $validated['stage']]);

        return back();
    }
}
