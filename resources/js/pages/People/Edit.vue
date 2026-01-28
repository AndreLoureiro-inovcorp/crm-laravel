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
    entity_id?: number | null
    name: string
    email?: string | null
    phone?: string | null
    position?: string | null
    notes?: string | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { person, entities } = defineProps<{
    person: Person
    entities: Entity[]
}>()

/* -------------------------------------------------------------------------- */
/* FORM */
/* -------------------------------------------------------------------------- */

const form = useForm({
    entity_id: person.entity_id ?? null,
    name: person.name,
    email: person.email ?? '',
    phone: person.phone ?? '',
    position: person.position ?? '',
    notes: person.notes ?? '',
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function submit() {
    form.put(`/people/${person.id}`, {
        preserveScroll: true,
    })
}

function cancel() {
    router.visit('/people')
}
</script>

<template>
    <AppLayout>

        <Head title="Editar Pessoa" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-6">Editar Pessoa</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nome -->
                            <div>
                                <Label for="name">Nome *</Label>
                                <Input id="name" v-model="form.name" type="text" required class="mt-1" />
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.name }}
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

                            <!-- Email -->
                            <div>
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" class="mt-1" />
                                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div>
                                <Label for="phone">Telefone</Label>
                                <Input id="phone" v-model="form.phone" type="text" class="mt-1" />
                                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <!-- Cargo -->
                            <div>
                                <Label for="position">Cargo</Label>
                                <Input id="position" v-model="form.position" type="text" class="mt-1" />
                                <div v-if="form.errors.position" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.position }}
                                </div>
                            </div>

                            <!-- Notas -->
                            <div>
                                <Label for="notes">Notas</Label>
                                <Textarea id="notes" v-model="form.notes" :rows="3" class="mt-1" />
                                <div v-if="form.errors.notes" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.notes }}
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" @click="cancel">
                                    Cancelar
                                </Button>

                                <Button type="submit" :disabled="form.processing">
                                    Atualizar Pessoa
                                </Button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>