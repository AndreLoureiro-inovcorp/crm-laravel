<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Person;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $people = Person::query()
            ->with('entity:id,name')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('People/Index', [
            'people' => $people,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entities = Entity::select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('People/Create', [
            'entities' => $entities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        Person::create($validated);

        return redirect()->route('people.index')
            ->with('success', 'Pessoa criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        $this->authorize('view', $person);

        $person->load(['entity', 'deals', 'calendarEvents']);

        return Inertia::render('People/Show', [
            'person' => $person,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        $entities = Entity::select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('People/Edit', [
            'person' => $person,
            'entities' => $entities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $validated = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $person->update($validated);

        return redirect()->route('people.index')
            ->with('success', 'Pessoa atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $this->authorize('delete', $person);

        $person->delete();

        return redirect()->route('people.index')
            ->with('success', 'Pessoa eliminada com sucesso.');
    }
}
