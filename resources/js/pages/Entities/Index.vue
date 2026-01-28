<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
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

interface Entity {
    id: number
    name: string
    vat?: string | null
    email?: string | null
    phone?: string | null
    status: 'active' | 'inactive'
}

interface Pagination<T> {
    data: T[]
    from: number
    to: number
    total: number
    links: {
        url: string | null
        label: string
        active: boolean
    }[]
}

interface Filters {
    search?: string
    status?: string
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { entities, filters } = defineProps<{
    entities: Pagination<Entity>
    filters?: Filters
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const search = ref(filters?.search ?? '')
const status = ref(filters?.status ?? '')

/* -------------------------------------------------------------------------- */
/* HELPERS */
/* -------------------------------------------------------------------------- */

function decodeLabel(label: string) {
    const txt = document.createElement('textarea')
    txt.innerHTML = label
    return txt.value
}

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function applyFilters() {
    router.get(
        '/entities',
        {
            search: search.value,
            status: status.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

function deleteEntity(id: number) {
    if (confirm('Tem a certeza que deseja eliminar esta entidade?')) {
        router.delete(`/entities/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Entidades" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Entidades</h1>
                            <Link href="/entities/create">
                                <Button>Criar Entidade</Button>
                            </Link>
                        </div>

                        <!-- Filters -->
                        <div class="flex gap-4 mb-6">
                            <Input v-model="search" placeholder="Pesquisar por nome, NIF ou email..."
                                @keyup.enter="applyFilters" class="max-w-sm" />

                            <select v-model="status" @change="applyFilters" class="border rounded px-3 py-2">
                                <option value="">Todos os estados</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>

                            <Button @click="applyFilters" variant="outline">
                                Filtrar
                            </Button>
                        </div>

                        <!-- Table -->
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>NIF</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Telefone</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="entity in entities.data" :key="entity.id">
                                    <TableCell class="font-medium">
                                        {{ entity.name }}
                                    </TableCell>

                                    <TableCell>{{ entity.vat ?? '-' }}</TableCell>
                                    <TableCell>{{ entity.email ?? '-' }}</TableCell>
                                    <TableCell>{{ entity.phone ?? '-' }}</TableCell>

                                    <TableCell>
                                        <Badge :variant="entity.status === 'active' ? 'default' : 'secondary'">
                                            {{ entity.status === 'active' ? 'Ativo' : 'Inativo' }}
                                        </Badge>
                                    </TableCell>

                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/entities/${entity.id}`">
                                                <Button variant="outline" size="sm">Ver</Button>
                                            </Link>

                                            <Link :href="`/entities/${entity.id}/edit`">
                                                <Button variant="outline" size="sm">Editar</Button>
                                            </Link>

                                            <Button variant="destructive" size="sm" @click="deleteEntity(entity.id)">
                                                Eliminar
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                Mostrando
                                {{ entities.from }}
                                a
                                {{ entities.to }}
                                de
                                {{ entities.total }}
                                registos
                            </div>

                            <div class="flex gap-2">
                                <Link v-for="link in entities.links" :key="link.label" :href="link.url ?? ''" :class="[
                                    'px-3 py-1 rounded',
                                    link.active
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-200',
                                    !link.url
                                        ? 'opacity-50 cursor-not-allowed'
                                        : 'hover:bg-gray-300',
                                ]">
                                    {{ decodeLabel(link.label) }}
                                </Link>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
