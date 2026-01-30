<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface Entity {
    id: number
    name: string
}

interface Person {
    id: number
    name: string
}

interface Deal {
    id: number
    name: string
}

interface CalendarEvent {
    id: number
    title: string
    description?: string | null
    start_at: string
    end_at: string
    location?: string | null
    eventable_type?: string | null
    eventable_id?: number | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { event, entities, people, deals } = defineProps<{
    event: CalendarEvent
    entities: Entity[]
    people: Person[]
    deals: Deal[]
}>()

/* -------------------------------------------------------------------------- */
/* FORM */
/* -------------------------------------------------------------------------- */

const form = useForm({
    eventable_type: event.eventable_type ?? null,
    eventable_id: event.eventable_id ?? null,
    title: event.title,
    description: event.description ?? '',
    start_at: event.start_at,
    end_at: event.end_at,
    location: event.location ?? '',
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function submit() {
    form.put(`/calendar/${event.id}`, {
        preserveScroll: true,
    })
}

function cancel() {
    router.visit('/calendar')
}
</script>

<template>
    <AppLayout>

        <Head title="Editar Evento" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-6">Editar Evento</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Título -->
                            <div>
                                <Label for="title">Título *</Label>
                                <Input id="title" v-model="form.title" type="text" required class="mt-1" />
                                <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Tipo de Associação -->
                            <div>
                                <Label for="eventable_type">Associar a</Label>
                                <select id="eventable_type" v-model="form.eventable_type"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Sem associação</option>
                                    <option value="Entity">Entidade</option>
                                    <option value="Person">Pessoa</option>
                                    <option value="Deal">Negócio</option>
                                </select>
                            </div>

                            <!-- Seleção de Entidade -->
                            <div v-if="form.eventable_type === 'Entity'">
                                <Label for="entity_id">Entidade</Label>
                                <select id="entity_id" v-model="form.eventable_id"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Selecione uma entidade</option>
                                    <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                        {{ entity.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Seleção de Pessoa -->
                            <div v-if="form.eventable_type === 'Person'">
                                <Label for="person_id">Pessoa</Label>
                                <select id="person_id" v-model="form.eventable_id"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Selecione uma pessoa</option>
                                    <option v-for="person in people" :key="person.id" :value="person.id">
                                        {{ person.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Seleção de Negócio -->
                            <div v-if="form.eventable_type === 'Deal'">
                                <Label for="deal_id">Negócio</Label>
                                <select id="deal_id" v-model="form.eventable_id"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Selecione um negócio</option>
                                    <option v-for="deal in deals" :key="deal.id" :value="deal.id">
                                        {{ deal.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <Label for="description">Descrição</Label>
                                <Textarea id="description" v-model="form.description" :rows="3" class="mt-1" />
                                <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <!-- Data/Hora Início -->
                            <div>
                                <Label for="start_at">Data/Hora Início *</Label>
                                <Input id="start_at" v-model="form.start_at" type="datetime-local" required
                                    class="mt-1" />
                                <div v-if="form.errors.start_at" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.start_at }}
                                </div>
                            </div>

                            <!-- Data/Hora Fim -->
                            <div>
                                <Label for="end_at">Data/Hora Fim *</Label>
                                <Input id="end_at" v-model="form.end_at" type="datetime-local" required class="mt-1" />
                                <div v-if="form.errors.end_at" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.end_at }}
                                </div>
                            </div>

                            <!-- Localização -->
                            <div>
                                <Label for="location">Localização</Label>
                                <Input id="location" v-model="form.location" type="text" class="mt-1" />
                                <div v-if="form.errors.location" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.location }}
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" @click="cancel">
                                    Cancelar
                                </Button>

                                <Button type="submit" :disabled="form.processing">
                                    Atualizar Evento
                                </Button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>