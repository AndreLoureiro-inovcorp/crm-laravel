<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\FormSubmission;
use App\Models\PublicForm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicFormController extends Controller
{
    /**
     * Display a listing of forms.
     */
    public function index()
    {
        $forms = PublicForm::withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Forms/Index', [
            'forms' => $forms,
        ]);
    }

    /**
     * Show the form for creating a new form.
     */
    public function create()
    {
        return Inertia::render('Forms/Create');
    }

    /**
     * Store a newly created form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fields' => 'required|array',
            'confirmation_message' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        $form = PublicForm::create($validated);

        // Generate embed code
        $embedCode = $this->generateEmbedCode($form->id);
        $form->update(['embed_code' => $embedCode]);

        return redirect()->route('forms.index')
            ->with('success', 'Formulário criado com sucesso.');
    }

    /**
     * Display the specified form.
     */
    public function show(PublicForm $form)
    {
        $form->load('submissions');

        return Inertia::render('Forms/Show', [
            'form' => $form,
        ]);
    }

    /**
     * Toggle form active status.
     */
    public function toggle(PublicForm $form)
    {
        $form->update([
            'is_active' => ! $form->is_active,
        ]);

        return back()->with('success', 'Estado do formulário atualizado.');
    }

    /**
     * Remove the specified form.
     */
    public function destroy(PublicForm $form)
    {
        $form->delete();

        return redirect()->route('forms.index')
            ->with('success', 'Formulário eliminado com sucesso.');
    }

    /**
     * Display public form.
     */
    public function showPublic($id)
    {
        $form = PublicForm::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('Forms/Public', [
            'form' => $form,
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    /**
     * Store a form submission.
     */
    public function submit(Request $request, $id)
    {
        $form = PublicForm::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        // Validate reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if (! $this->verifyRecaptcha($recaptchaResponse)) {
            return back()->withErrors(['recaptcha' => 'Por favor, complete o reCAPTCHA.']);
        }

        // Store submission
        FormSubmission::create([
            'public_form_id' => $form->id,
            'data' => $request->except(['_token', 'g-recaptcha-response']),
            'ip_address' => $request->ip(),
            'submitted_at' => now(),
        ]);

        // Create Lead (Entity)
        Entity::create([
            'tenant_id' => $form->tenant_id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'type' => 'lead',
            'source' => 'Formulário Público: '.$form->name,
        ]);

        return redirect()->back()->with('success', $form->confirmation_message ?? 'Obrigado! A sua submissão foi recebida.');
    }

    /**
     * Generate embed code for form.
     */
    private function generateEmbedCode($formId): string
    {
        $url = url("/form/{$formId}");

        return <<<HTML
<!-- Formulário Lead Generation -->
<iframe src="{$url}" width="100%" height="600" frameborder="0"></iframe>
<!-- Fim Formulário -->
HTML;
    }

    /**
     * Verify reCAPTCHA response.
     */
    private function verifyRecaptcha($response): bool
    {
        $secretKey = config('services.recaptcha.secret_key');

        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}");
        $responseData = json_decode($verifyResponse);

        return $responseData->success ?? false;
    }
}
