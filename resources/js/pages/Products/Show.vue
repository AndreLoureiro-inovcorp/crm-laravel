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

interface Deal {
    id: number
    title: string
    stage: string
    value: number
    entity?: Entity | null
    person?: Person | null
    owner?: User | null
}

interface DealProduct {
    id: number
    deal_id: number
    product_name: string
    quantity: number
    unit_price: number
    total_price: number
    deal_title: string
    stage: string
    deal_value: number
    deal?: Deal
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { product_name, deals } = defineProps<{
    product_name: string
    deals: DealProduct[]
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

function getTotalQuantity(): number {
    return deals.reduce((sum, deal) => sum + Number(deal.quantity), 0)
}

function getTotalValue(): number {
    return deals.reduce((sum, deal) => sum + Number(deal.total_price), 0)
}
</script>

<template>
    <AppLayout>

        <Head :title="`Produto: ${product_name}`" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">{{ product_name }}</h1>
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                            <span>{{ deals.length }} negócios</span>
                            <span>{{ getTotalQuantity() }} unidades</span>
                            <span class="font-semibold text-green-600">
                                {{ formatCurrency(getTotalValue()) }}
                            </span>
                        </div>
                    </div>

                    <Link href="/products/stats">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <h2 class="text-xl font-bold mb-4">Negócios com este Produto</h2>

                        <Table v-if="deals.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Negócio</TableHead>
                                    <TableHead>Etapa</TableHead>
                                    <TableHead>Cliente</TableHead>
                                    <TableHead class="text-right">Quantidade</TableHead>
                                    <TableHead class="text-right">Preço Unit.</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="deal in deals" :key="deal.id">
                                    <TableCell class="font-medium">
                                        {{ deal.deal_title }}
                                    </TableCell>

                                    <TableCell>
                                        <Badge>{{ deal.stage }}</Badge>
                                    </TableCell>

                                    <TableCell>
                                        {{ deal.deal?.entity?.name ?? deal.deal?.person?.name ?? '-' }}
                                    </TableCell>

                                    <TableCell class="text-right">
                                        {{ deal.quantity }}
                                    </TableCell>

                                    <TableCell class="text-right">
                                        {{ formatCurrency(deal.unit_price) }}
                                    </TableCell>

                                    <TableCell class="text-right font-semibold text-green-600">
                                        {{ formatCurrency(deal.total_price) }}
                                    </TableCell>

                                    <TableCell class="text-right">
                                        <Link :href="`/deals/${deal.deal_id}`">
                                            <Button variant="outline" size="sm">Ver Negócio</Button>
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <p v-else class="text-gray-500 text-center py-8">
                            Nenhum negócio encontrado para este produto.
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>