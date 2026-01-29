<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

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

interface Entity {
    id: number
    name: string
}

interface Person {
    id: number
    name: string
}

interface User {
    id: number
    name: string
}

interface DealProduct {
    id: number
    product_name: string
    quantity: number
    unit_price: number
    total_price: number
}

interface DealActivity {
    id: number
    type: string
    description: string
    occurred_at: string
    user?: User
}

interface DealEmail {
    id: number
    type: string
    subject: string
    sent_at: string
}

interface DealProposal {
    id: number
    file_path: string
    sent_at?: string | null
}

interface CalendarEvent {
    id: number
    title: string
    start_at: string
    end_at: string
}

interface Deal {
    id: number
    title: string
    value: number
    stage: string
    probability: number
    expected_close_date?: string | null
    entity?: Entity | null
    person?: Person | null
    owner?: User | null
    products?: DealProduct[]
    activities?: DealActivity[]
    emails?: DealEmail[]
    proposal?: DealProposal | null
    calendar_events?: CalendarEvent[]
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { deal } = defineProps<{
    deal: Deal
}>()

/* -------------------------------------------------------------------------- */
/* HELPERS */
/* -------------------------------------------------------------------------- */

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}
</script>

<template>
    <AppLayout>

        <Head :title="deal.title" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h1 class="text-2xl font-bold">{{ deal.title }}</h1>
                                <div class="flex items-center gap-3 mt-2">
                                    <Badge>{{ deal.stage }}</Badge>
                                    <span class="text-lg font-semibold text-green-600">
                                        {{ formatCurrency(deal.value) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <Link :href="`/deals/${deal.id}/edit`">
                                    <Button>Editar</Button>
                                </Link>

                                <Link href="/deals">
                                    <Button variant="outline">Voltar</Button>
                                </Link>
                            </div>
                        </div>

                        <!-- Informação do Negócio -->
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Entidade</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ deal.entity?.name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Pessoa</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ deal.person?.name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Responsável</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ deal.owner?.name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Probabilidade</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ deal.probability }}%
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Data Prevista de Fecho</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ deal.expected_close_date
                                        ? new Date(deal.expected_close_date).toLocaleDateString('pt-PT')
                                        : '-'
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Produtos -->
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">Produtos</h2>

                            <Table v-if="deal.products?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Produto</TableHead>
                                        <TableHead>Quantidade</TableHead>
                                        <TableHead>Preço Unitário</TableHead>
                                        <TableHead>Total</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="product in deal.products" :key="product.id">
                                        <TableCell class="font-medium">
                                            {{ product.product_name }}
                                        </TableCell>
                                        <TableCell>{{ product.quantity }}</TableCell>
                                        <TableCell>{{ formatCurrency(product.unit_price) }}</TableCell>
                                        <TableCell>{{ formatCurrency(product.total_price) }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhum produto associado.
                            </p>
                        </div>

                        <!-- Atividades -->
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">Atividades</h2>

                            <Table v-if="deal.activities?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Tipo</TableHead>
                                        <TableHead>Descrição</TableHead>
                                        <TableHead>Data</TableHead>
                                        <TableHead>Utilizador</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="activity in deal.activities" :key="activity.id">
                                        <TableCell>
                                            <Badge variant="outline">{{ activity.type }}</Badge>
                                        </TableCell>
                                        <TableCell class="font-medium">
                                            {{ activity.description }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(activity.occurred_at).toLocaleString('pt-PT') }}
                                        </TableCell>
                                        <TableCell>{{ activity.user?.name ?? '-' }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhuma atividade registada.
                            </p>
                        </div>

                        <!-- Emails -->
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">Emails Enviados</h2>

                            <Table v-if="deal.emails?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Tipo</TableHead>
                                        <TableHead>Assunto</TableHead>
                                        <TableHead>Enviado em</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="email in deal.emails" :key="email.id">
                                        <TableCell>
                                            <Badge>{{ email.type }}</Badge>
                                        </TableCell>
                                        <TableCell class="font-medium">
                                            {{ email.subject }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(email.sent_at).toLocaleString('pt-PT') }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhum email enviado.
                            </p>
                        </div>

                        <!-- Proposta -->
                        <div class="mb-8">
                            <h2 class="text-xl font-bold mb-4">Proposta</h2>

                            <div v-if="deal.proposal" class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium">
                                    Ficheiro: {{ deal.proposal.file_path }}
                                </p>
                                <p v-if="deal.proposal.sent_at" class="text-sm text-gray-600 mt-1">
                                    Enviado em: {{ new Date(deal.proposal.sent_at).toLocaleString('pt-PT') }}
                                </p>
                            </div>

                            <p v-else class="text-gray-500 text-sm">
                                Nenhuma proposta carregada.
                            </p>
                        </div>

                        <!-- Eventos -->
                        <div>
                            <h2 class="text-xl font-bold mb-4">Eventos</h2>

                            <Table v-if="deal.calendar_events?.length">
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Título</TableHead>
                                        <TableHead>Início</TableHead>
                                        <TableHead>Fim</TableHead>
                                    </TableRow>
                                </TableHeader>

                                <TableBody>
                                    <TableRow v-for="event in deal.calendar_events" :key="event.id">
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