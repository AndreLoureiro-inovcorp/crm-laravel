<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

interface Field {
    id: string
    label: string
    type: string
    required: boolean
}

const fields = ref<Field[]>([
    { id: 'name', label: 'Nome', type: 'text', required: true },
    { id: 'email', label: 'Email', type: 'email', required: true },
    { id: 'phone', label: 'Telefone', type: 'text', required: false },
])

const form = useForm({
    name: '',
    fields: fields.value,
    confirmation_message: 'Obrigado! A sua submissão foi recebida com sucesso.',
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function addField() {
    fields.value.push({
        id: `field_${Date.now()}`,
        label: '',
        type: 'text',
        required: false,
    })
}

function removeField(index: number) {
    fields.value.splice(index, 1)
}

function submit() {
    form.fields = fields.value
    form.post('/forms')
}
</script>

<template>
    <AppLayout>

        <Head title="Criar Formulário" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <h1 class="text-2xl font-bold mb-6">Criar Formulário Público</h1>

                        <form @submit.prevent="submit" class="space-y-6">

                            <div>
                                <Label for="name">Nome do Formulário *</Label>
                                <Input id="name" v-model="form.name" type="text" class="mt-1"
                                    placeholder="Ex: Contacto de Leads" />
                                <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <Label>Campos do Formulário</Label>
                                    <Button type="button" variant="outline" size="sm" @click="addField">
                                        + Adicionar Campo
                                    </Button>
                                </div>

                                <div v-for="(field, index) in fields" :key="field.id"
                                    class="grid grid-cols-12 gap-4 mb-4 p-4 border rounded">

                                    <div class="col-span-4">
                                        <Label>Etiqueta</Label>
                                        <Input v-model="field.label" type="text" placeholder="Ex: Nome" class="mt-1" />
                                    </div>

                                    <div class="col-span-3">
                                        <Label>Tipo</Label>
                                        <select v-model="field.type" class="mt-1 w-full border rounded px-3 py-2">
                                            <option value="text">Texto</option>
                                            <option value="email">Email</option>
                                            <option value="tel">Telefone</option>
                                            <option value="textarea">Textarea</option>
                                        </select>
                                    </div>

                                    <div class="col-span-3 flex items-end">
                                        <label class="flex items-center gap-2">
                                            <input v-model="field.required" type="checkbox" class="rounded" />
                                            <span class="text-sm">Obrigatório</span>
                                        </label>
                                    </div>

                                    <div class="col-span-2 flex items-end">
                                        <Button type="button" variant="destructive" size="sm"
                                            @click="removeField(index)">
                                            Remover
                                        </Button>
                                    </div>

                                </div>
                            </div>

                            <div>
                                <Label for="confirmation_message">Mensagem de Confirmação</Label>
                                <Textarea id="confirmation_message" v-model="form.confirmation_message" :rows="3"
                                    class="mt-1" />
                                <p v-if="form.errors.confirmation_message" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.confirmation_message }}
                                </p>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" @click="$inertia.visit('/forms')">
                                    Cancelar
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    Criar Formulário
                                </Button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>