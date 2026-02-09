<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, nextTick, computed } from 'vue'
import axios from 'axios'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface Message {
    role: 'user' | 'assistant'
    content: string
}

interface Conversation {
    id: number
    messages: Message[]
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { conversation } = defineProps<{
    conversation?: Conversation | null
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const messages = ref<Message[]>(conversation?.messages ?? [])
const conversationId = ref<number | null>(conversation?.id ?? null)
const userMessage = ref('')
const isLoading = ref(false)
const messagesContainer = ref<HTMLElement>()

/* -------------------------------------------------------------------------- */
/* COMPUTED */
/* -------------------------------------------------------------------------- */

const hasMessages = computed(() => messages.value.length > 0)

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

async function sendMessage() {
    if (!userMessage.value.trim() || isLoading.value) return

    const messageText = userMessage.value
    userMessage.value = ''
    isLoading.value = true

    // Add user message immediately
    messages.value.push({
        role: 'user',
        content: messageText,
    })

    scrollToBottom()

    try {
        const response = await axios.post('/chat/send', {
            message: messageText,
            conversation_id: conversationId.value,
        })

        conversationId.value = response.data.conversation_id

        // Add AI response
        messages.value.push({
            role: 'assistant',
            content: response.data.message,
        })

        scrollToBottom()
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error)

        messages.value.push({
            role: 'assistant',
            content: 'Desculpa, ocorreu um erro. Por favor tenta novamente.',
        })
    } finally {
        isLoading.value = false
    }
}

function clearConversation() {
    if (!confirm('Tem a certeza que deseja limpar a conversa?')) return

    router.delete('/chat/clear', {
        onSuccess: () => {
            messages.value = []
            conversationId.value = null
        },
    })
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

/* -------------------------------------------------------------------------- */
/* LIFECYCLE */
/* -------------------------------------------------------------------------- */

scrollToBottom()
</script>

<template>
    <AppLayout>

        <Head title="Chat Inteligente" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">Chat Inteligente</h1>
                        <p class="text-gray-600 text-sm mt-1">
                            Faz perguntas sobre o teu CRM
                        </p>
                    </div>
                    <Button v-if="hasMessages" variant="outline" @click="clearConversation">
                        Limpar Conversa
                    </Button>
                </div>

                <!-- Chat Container -->
                <div class="bg-white rounded-lg shadow-sm border">

                    <!-- Messages Area -->
                    <div ref="messagesContainer" class="h-[600px] p-6 overflow-y-auto">
                        <div v-if="!hasMessages" class="flex items-center justify-center h-full">
                            <div class="text-center text-gray-500">
                                <p class="text-lg font-medium mb-2">👋 Olá! Como posso ajudar?</p>
                                <p class="text-sm">Faz uma pergunta sobre entidades, pessoas ou negócios</p>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(message, index) in messages" :key="index" :class="[
                                'flex',
                                message.role === 'user' ? 'justify-end' : 'justify-start'
                            ]">
                                <div :class="[
                                    'max-w-[80%] rounded-lg px-4 py-3',
                                    message.role === 'user'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-900'
                                ]">
                                    <p class="text-sm whitespace-pre-wrap">{{ message.content }}</p>
                                </div>
                            </div>

                            <div v-if="isLoading" class="flex justify-start">
                                <div class="bg-gray-100 rounded-lg px-4 py-3">
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                            style="animation-delay: 0.2s"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                            style="animation-delay: 0.4s"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="border-t p-4">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <Input v-model="userMessage" type="text" placeholder="Escreve a tua pergunta..."
                                :disabled="isLoading" class="flex-1" />
                            <Button type="submit" :disabled="!userMessage.trim() || isLoading">
                                Enviar
                            </Button>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </AppLayout>
</template>