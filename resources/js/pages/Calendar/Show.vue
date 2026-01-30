<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface User {
    id: number
    name: string
}

interface Eventable {
    id: number
    name?: string
    title?: string
}

interface CalendarEvent {
    id: number
    title: string
    description?: string | null
    start_at: string
    end_at: string
    location?: string | null
    owner?: User | null
    eventable_type?: string | null
    eventable?: Eventable | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { event } = defineProps<{
    event: CalendarEvent
}>()

/* -------------------------------------------------------------------------- */
/* HELPERS */
/* -------------------------------------------------------------------------- */

function getEventableName(): string {
    if (!event.eventable) return '-'
    return event.eventable.name ?? event.eventable.title ?? '-'
}
</script>

<template>
    <AppLayout>

        <Head :title="event.title" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="text-2xl font-bold">{{ event.title }}</h1>
                                <Badge v-if="event.eventable_type" class="mt-2">
                                    {{ event.eventable_type }}
                                </Badge>
                            </div>

                            <div class="flex gap-2">
                                <Link :href="`/calendar/${event.id}/edit`">
                                    <Button>Editar</Button>
                                </Link>

                                <Link href="/calendar">
                                    <Button variant="outline">Voltar</Button>
                                </Link>
                            </div>
                        </div>

                        <!-- Informação do Evento -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Descrição</h3>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                    {{ event.description ?? '-' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Data/Hora Início</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ new Date(event.start_at).toLocaleString('pt-PT') }}
                                    </p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Data/Hora Fim</h3>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ new Date(event.end_at).toLocaleString('pt-PT') }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Localização</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ event.location ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Responsável</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ event.owner?.name ?? '-' }}
                                </p>
                            </div>

                            <div v-if="event.eventable_type">
                                <h3 class="text-sm font-medium text-gray-500">
                                    Associado a {{ event.eventable_type }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ getEventableName() }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>