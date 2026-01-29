<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
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
    entity_id?: number | null
}

interface DealStage {
    id: number
    name: string
}

interface Deal {
    id: number
    entity_id?: number | null
    person_id?: number | null
    title: string
    value: number
    stage: string
    probability: number
    expected_close_date?: string | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { deal, entities, people, stages } = defineProps<{
    deal: Deal
    entities: Entity[]
    people: Person[]
    stages: DealStage[]
}>()

/* -------------------------------------------------------------------------- */
/* FORM */
/* -------------------------------------------------------------------------- */

const form = useForm({
    entity_id: deal.entity_id ?? null,
    person_id: deal.person_id ?? null,
    title: deal.title,
    value: deal.value.toString(),
    stage: deal.stage,
    probability: deal.probability.toString(),
    expected_close_date: deal.expected_close_date ?? '',
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function submit() {
    form.put(`/deals/${deal.id}`, {
        preserveScroll: true,
    })
}

function cancel() {
    router.visit('/deals')
}
</script>

<template>
    <AppLayout>

        <Head title="Editar Negócio" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-6">Editar Negócio</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Título -->
                            <div>
                                <Label for="title">Título *</Label>
                                <Input id="title" v-model="form.title" type="text" required class="mt-1" />
                                <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Entidade -->
                            <div>
                                <Label for="entity_id">Entidade</Label>
                                <select id="entity_id" v-model="form.entity_id"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Sem entidade associada</option>
                                    <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                        {{ entity.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.entity_id" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.entity_id }}
                                </div>
                            </div>

                            <!-- Pessoa -->
                            <div>
                                <Label for="person_id">Pessoa</Label>
                                <select id="person_id" v-model="form.person_id"
                                    class="mt-1 w-full border rounded px-3 py-2">
                                    <option :value="null">Sem pessoa associada</option>
                                    <option v-for="person in people" :key="person.id" :value="person.id">
                                        {{ person.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.person_id" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.person_id }}
                                </div>
                            </div>

                            <!-- Valor -->
                            <div>
                                <Label for="value">Valor *</Label>
                                <Input id="value" v-model="form.value" type="number" step="0.01" required
                                    class="mt-1" />
                                <div v-if="form.errors.value" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.value }}
                                </div>
                            </div>

                            <!-- Etapa -->
                            <div>
                                <Label for="stage">Etapa *</Label>
                                <select id="stage" v-model="form.stage" class="mt-1 w-full border rounded px-3 py-2"
                                    required>
                                    <option v-for="stage in stages" :key="stage.id" :value="stage.name">
                                        {{ stage.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.stage" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.stage }}
                                </div>
                            </div>

                            <!-- Probabilidade -->
                            <div>
                                <Label for="probability">Probabilidade (%) *</Label>
                                <Input id="probability" v-model="form.probability" type="number" min="0" max="100"
                                    required class="mt-1" />
                                <div v-if="form.errors.probability" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.probability }}
                                </div>
                            </div>

                            <!-- Data Prevista de Fecho -->
                            <div>
                                <Label for="expected_close_date">Data Prevista de Fecho</Label>
                                <Input id="expected_close_date" v-model="form.expected_close_date" type="date"
                                    class="mt-1" />
                                <div v-if="form.errors.expected_close_date" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.expected_close_date }}
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" @click="cancel">
                                    Cancelar
                                </Button>

                                <Button type="submit" :disabled="form.processing">
                                    Atualizar Negócio
                                </Button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>