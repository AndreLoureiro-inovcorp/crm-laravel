<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface Field {
    id: string
    label: string
    type: string
    required: boolean
}

interface Submission {
    id: number
    data: Record<string, unknown>
    ip_address: string
    submitted_at: string
}

interface PublicForm {
    id: number
    name: string
    fields: Field[]
    embed_code: string
    confirmation_message: string
    is_active: boolean
    submissions: Submission[]
    created_at: string
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { form } = defineProps<{
    form: PublicForm
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const showEmbedCode = ref(false)

/* -------------------------------------------------------------------------- */
/* COMPUTED */
/* -------------------------------------------------------------------------- */

const publicUrl = computed(() => {
    if (typeof window === 'undefined') return ''
    return `${window.location.origin}/form/${form.id}`
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function copyEmbedCode() {
    navigator.clipboard.writeText(form.embed_code)
    alert('Código copiado para a área de transferência!')
}
</script>

<template>
    <AppLayout>

        <Head :title="form.name" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">{{ form.name }}</h1>
                        <Badge :variant="form.is_active ? 'default' : 'secondary'" class="mt-2">
                            {{ form.is_active ? 'Ativo' : 'Inativo' }}
                        </Badge>
                    </div>
                    <Link href="/forms">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>

                <!-- Código Embed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Código de Incorporação</h2>
                            <Button @click="showEmbedCode = !showEmbedCode">
                                {{ showEmbedCode ? 'Ocultar' : 'Mostrar' }} Código
                            </Button>
                        </div>

                        <div v-if="showEmbedCode" class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded">
                                <pre class="text-sm overflow-x-auto">{{ form.embed_code }}</pre>
                            </div>
                            <Button @click="copyEmbedCode">Copiar Código</Button>
                        </div>

                        <p class="text-sm text-gray-600 mt-4">
                            URL Público:
                            <a :href="publicUrl" target="_blank" class="text-blue-600 underline">
                                {{ publicUrl }}
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Campos do Formulário -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-4">Campos</h2>

                        <div class="space-y-3">
                            <div v-for="field in form.fields" :key="field.id"
                                class="flex items-center gap-4 p-3 border rounded">
                                <div class="flex-1">
                                    <p class="font-medium">{{ field.label }}</p>
                                    <p class="text-sm text-gray-500">Tipo: {{ field.type }}</p>
                                </div>
                                <Badge v-if="field.required" variant="outline">Obrigatório</Badge>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submissões -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-4">
                            Submissões ({{ form.submissions.length }})
                        </h2>

                        <Table v-if="form.submissions.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Dados</TableHead>
                                    <TableHead>IP</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="submission in form.submissions" :key="submission.id">
                                    <TableCell>
                                        {{
                                            new Date(
                                                submission.submitted_at
                                            ).toLocaleString('pt-PT')
                                        }}
                                    </TableCell>
                                    <TableCell>
                                        <div class="text-sm">
                                            <div v-for="(value, key) in submission.data" :key="key">
                                                <strong>{{ key }}:</strong>
                                                {{ value }}
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>{{ submission.ip_address }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <p v-else class="text-gray-500 text-center py-8">
                            Nenhuma submissão ainda.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>