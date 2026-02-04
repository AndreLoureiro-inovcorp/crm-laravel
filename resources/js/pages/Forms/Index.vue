<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'

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

interface PublicForm {
    id: number
    name: string
    is_active: boolean
    submissions_count: number
    created_at: string
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { forms } = defineProps<{
    forms: PublicForm[]
}>()

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function toggleForm(formId: number) {
    router.patch(`/forms/${formId}/toggle`, {}, {
        preserveScroll: true,
    })
}

function deleteForm(formId: number) {
    if (confirm('Tem a certeza que deseja eliminar este formulário?')) {
        router.delete(`/forms/${formId}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Formulários Públicos" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Formulários Públicos</h1>
                    <Link href="/forms/create">
                        <Button>+ Novo Formulário</Button>
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <Table v-if="forms.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead>Submissões</TableHead>
                                    <TableHead>Criado em</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="form in forms" :key="form.id">
                                    <TableCell class="font-medium">
                                        {{ form.name }}
                                    </TableCell>

                                    <TableCell>
                                        <Badge :variant="form.is_active ? 'default' : 'secondary'">
                                            {{ form.is_active ? 'Ativo' : 'Inativo' }}
                                        </Badge>
                                    </TableCell>

                                    <TableCell>
                                        {{ form.submissions_count }}
                                    </TableCell>

                                    <TableCell>
                                        {{ new Date(form.created_at).toLocaleDateString('pt-PT') }}
                                    </TableCell>

                                    <TableCell class="text-right space-x-2">
                                        <Link :href="`/forms/${form.id}`">
                                            <Button variant="outline" size="sm">Ver</Button>
                                        </Link>
                                        <Button variant="outline" size="sm" @click="toggleForm(form.id)">
                                            {{ form.is_active ? 'Desativar' : 'Ativar' }}
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="deleteForm(form.id)">
                                            Eliminar
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <p v-else class="text-gray-500 text-center py-8">
                            Nenhum formulário criado. Crie o seu primeiro formulário público!
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>