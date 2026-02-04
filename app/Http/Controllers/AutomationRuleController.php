<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AutomationRuleController extends Controller
{
    /**
     * Display a listing of automation rules.
     */
    public function index()
    {
        $rules = AutomationRule::orderBy('created_at', 'desc')->get();

        return Inertia::render('Automations/Index', [
            'rules' => $rules,
        ]);
    }

    /**
     * Store a newly created automation rule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|in:no_activity,stage_stuck',
            'trigger_value' => 'required|integer|min:1',
            'action_type' => 'required|in:create_task,send_notification',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        AutomationRule::create($validated);

        return back()->with('success', 'Automação criada com sucesso.');
    }

    /**
     * Toggle automation rule active status.
     */
    public function toggle(AutomationRule $automationRule)
    {
        $automationRule->update([
            'is_active' => ! $automationRule->is_active,
        ]);

        return back()->with('success', 'Automação atualizada com sucesso.');
    }

    /**
     * Remove the specified automation rule.
     */
    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();

        return back()->with('success', 'Automação eliminada com sucesso.');
    }
}
