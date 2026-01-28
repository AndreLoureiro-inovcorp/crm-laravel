<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

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
}

interface Person {
    id: number
    name: string
    email?: string | null
    phone?: string | null
    position?: string | null
    entity?: Entity | null
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
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { people, filters } = defineProps<{
    people: Pagination<Person>
    filters?: Filters
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const search = ref(filters?.search ?? '')

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
        '/people',
        {
            search: search.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

function deletePerson(id: number) {
    if (confirm('Tem a certeza que deseja eliminar esta pessoa?')) {
        router.delete(`/people/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Pessoas" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Pessoas</h1>
                            <Link href="/people/create">
                                <Button>Criar Pessoa</Button>
                            </Link>
                        </div>

                        <!-- Filters -->
                        <div class="flex gap-4 mb-6">
                            <Input v-model="search" placeholder="Pesquisar por nome, email ou telefone..."
                                @keyup.enter="applyFilters" class="max-w-sm" />

                            <Button @click="applyFilters" variant="outline">
                                Filtrar
                            </Button>
                        </div>

                        <!-- Table -->
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Entidade</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Telefone</TableHead>
                                    <TableHead>Cargo</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="person in people.data" :key="person.id">
                                    <TableCell class="font-medium">
                                        {{ person.name }}
                                    </TableCell>

                                    <TableCell>{{ person.entity?.name ?? '-' }}</TableCell>
                                    <TableCell>{{ person.email ?? '-' }}</TableCell>
                                    <TableCell>{{ person.phone ?? '-' }}</TableCell>
                                    <TableCell>{{ person.position ?? '-' }}</TableCell>

                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/people/${person.id}`">
                                                <Button variant="outline" size="sm">Ver</Button>
                                            </Link>

                                            <Link :href="`/people/${person.id}/edit`">
                                                <Button variant="outline" size="sm">Editar</Button>
                                            </Link>

                                            <Button variant="destructive" size="sm" @click="deletePerson(person.id)">
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
                                {{ people.from }}
                                a
                                {{ people.to }}
                                de
                                {{ people.total }}
                                registos
                            </div>

                            <div class="flex gap-2">
                                <Link v-for="link in people.links" :key="link.label" :href="link.url ?? ''" :class="[
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