<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EntityController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $entities = Entity::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('vat', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Entities/Index', [
            'entities' => $entities,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Entities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        Entity::create($validated);

        return redirect()->route('entities.index')
            ->with('success', 'Entidade criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        $this->authorize('view', $entity);

        $entity->load(['people', 'deals']);

        return Inertia::render('Entities/Show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        $this->authorize('update', $entity);

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entity $entity)
    {
        $this->authorize('update', $entity);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $entity->update($validated);

        return redirect()->route('entities.index')
            ->with('success', 'Entidade atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        $this->authorize('delete', $entity);

        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidade eliminada com sucesso.');
    }
}
