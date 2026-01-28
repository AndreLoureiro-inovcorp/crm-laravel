<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

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

interface Entity {
    id: number
    name: string
}

interface Deal {
    id: number
    title: string
    value: number
    stage: string
}

interface CalendarEvent {
    id: number
    title: string
    start_at: string
    end_at: string
}

interface Person {
    id: number
    name: string
    email?: string | null
    phone?: string | null
    position?: string | null
    notes?: string | null
    entity?: Entity | null
    deals?: Deal[]
    calendar_events?: CalendarEvent[]
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { person } = defineProps<{
    person: Person
}>()
</script>

<template>
    <AppLayout>

        <Head :title="person.name" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="text-2xl font-bold">{{ person.name }}</h1>
                                <p v-if="person.position" class="text-gray-600 mt-1">
                                    {{ person.position }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <Link :href="`/people/${person.id}/edit`">
                                    <Button>Editar</Button>
                                </Link>

                                <Link href="/people">
                                    <Button variant="outline">Voltar</Button>
                                </Link>
                            </div>
                        </div>

                        <!-- Informação da Pessoa -->
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Entidade</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ person.entity?.name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ person.email ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Telefone</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ person.phone ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Cargo</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ person.position ?? '-' }}
                                </p>
                            </div>

                            <div class="col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Notas</h3>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                    {{ person.notes ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Histórico de Negócios -->
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">Histórico de Negócios</h2>

                            <Table v-if="person.deals?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Título</TableHead>
                                        <TableHead>Valor</TableHead>
                                        <TableHead>Etapa</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="deal in person.deals" :key="deal.id">
                                        <TableCell class="font-medium">
                                            {{ deal.title }}
                                        </TableCell>
                                        <TableCell>{{ deal.value }}€</TableCell>
                                        <TableCell>{{ deal.stage }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhum negócio associado.
                            </p>
                        </div>

                        <!-- Histórico de Eventos -->
                        <div>
                            <h2 class="text-xl font-bold mb-4">Histórico de Eventos</h2>

                            <Table v-if="person.calendar_events?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Título</TableHead>
                                        <TableHead>Início</TableHead>
                                        <TableHead>Fim</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="event in person.calendar_events" :key="event.id">
                                        <TableCell class="font-medium">
                                            {{ event.title }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(event.start_at).toLocaleString('pt-PT') }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(event.end_at).toLocaleString('pt-PT') }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhum evento associado.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>