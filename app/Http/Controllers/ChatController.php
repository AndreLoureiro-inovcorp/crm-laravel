<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Deal;
use App\Models\Entity;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    public function index()
    {
        $conversation = ChatConversation::where('user_id', auth()->id())->latest()->first();

        return Inertia::render('Chat/Index', [
            'conversation' => $conversation,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'nullable|integer',
        ]);

        $conversation = null;

        if (! empty($validated['conversation_id'])) {
            $conversation = ChatConversation::where('id', $validated['conversation_id'])
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        $allMessages = $conversation?->messages ?? [];

        $allMessages[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        $recentMessages = array_slice($allMessages, -12);

        $context = $this->getCRMContext($validated['message']);
        $systemMessage = $this->buildSystemMessage($context);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ...$recentMessages,
            ],
        ]);

        $aiMessage = $response->choices[0]->message->content;

        $allMessages[] = [
            'role' => 'assistant',
            'content' => $aiMessage,
        ];

        if ($conversation) {
            $conversation->update(['messages' => $allMessages]);
        } else {
            $conversation = ChatConversation::create([
                'user_id' => auth()->id(),
                'messages' => $allMessages,
            ]);
        }

        return response()->json([
            'message' => $aiMessage,
            'conversation_id' => $conversation->id,
        ]);
    }

    private function getCRMContext(string $query): array
    {
        $tenantId = auth()->user()->tenant_id;
        $queryLower = strtolower($query);

        $context = [];

        if (str_contains($queryLower, ['entidade', 'cliente', 'empresa'])) {
            $context['entities'] = Entity::where('tenant_id', $tenantId)->select('name', 'email', 'phone', 'type')->limit(10)->get();
        }

        if (str_contains($queryLower, ['pessoa', 'contacto', 'telefone'])) {
            $context['people'] = Person::where('tenant_id', $tenantId)->select('name', 'email', 'phone', 'position')->limit(10)->get();
        }

        if (str_contains($queryLower, ['negócio', 'pipeline', 'valor', 'etapa'])) {
            $context['deals'] = Deal::where('tenant_id', $tenantId)->with(['entity:id,name', 'person:id,name', 'owner:id,name'])->select('title', 'value', 'stage', 'probability', 'entity_id', 'person_id', 'owner_id')->limit(10)->get();
        }

        return $context;
    }

    private function buildSystemMessage(array $context): string
    {
        $message = "És um assistente inteligente de CRM. Responde sempre em português de Portugal.\n\n";

        foreach ($context as $type => $items) {
            $message .= strtoupper($type).":\n";

            foreach ($items as $item) {
                $message .= json_encode($item, JSON_UNESCAPED_UNICODE)."\n";
            }

            $message .= "\n";
        }

        return $message;
    }

    public function clear()
    {
        ChatConversation::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }
}
