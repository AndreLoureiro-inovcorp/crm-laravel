<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
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

interface Product {
    product_name: string
    total_quantity: number
    total_value: number
    deal_count: number
}

interface Filters {
    stage?: string
    owner_id?: number
    start_date?: string
    end_date?: string
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { products, filters } = defineProps<{
    products: Product[]
    filters?: Filters
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const stage = ref<string>(filters?.stage ?? '')
const startDate = ref<string>(filters?.start_date ?? '')
const endDate = ref<string>(filters?.end_date ?? '')

/* -------------------------------------------------------------------------- */
/* HELPERS */
/* -------------------------------------------------------------------------- */

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function applyFilters() {
    router.get(
        '/products/stats',
        {
            stage: stage.value || undefined,
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

function exportCSV() {
    const params = new URLSearchParams({
        stage: stage.value || '',
        start_date: startDate.value || '',
        end_date: endDate.value || '',
    })

    window.location.href = `/products/stats/export?${params.toString()}`
}

function viewProduct(productName: string) {
    router.visit(`/products/stats/${encodeURIComponent(productName)}`)
}
</script>

<template>
    <AppLayout>

        <Head title="Estatísticas de Produtos" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Estatísticas de Produtos</h1>
                    <div class="flex gap-2">
                        <Button @click="exportCSV" variant="outline">
                            Exportar CSV
                        </Button>
                        <Link href="/deals">
                            <Button variant="outline">Voltar</Button>
                        </Link>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Filtros</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <Label for="stage">Etapa</Label>
                            <select id="stage" v-model="stage" class="mt-1 w-full border rounded px-3 py-2">
                                <option value="">Todas as etapas</option>
                                <option value="Lead">Lead</option>
                                <option value="Proposta">Proposta</option>
                                <option value="Negociação">Negociação</option>
                                <option value="Ganho">Ganho</option>
                                <option value="Perdido">Perdido</option>
                                <option value="Follow Up">Follow Up</option>
                            </select>
                        </div>

                        <div>
                            <Label for="start_date">Data Início</Label>
                            <Input id="start_date" v-model="startDate" type="date" class="mt-1" />
                        </div>

                        <div>
                            <Label for="end_date">Data Fim</Label>
                            <Input id="end_date" v-model="endDate" type="date" class="mt-1" />
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <Button @click="applyFilters">Aplicar Filtros</Button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <Table v-if="products.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Produto</TableHead>
                                    <TableHead class="text-right">Quantidade Total</TableHead>
                                    <TableHead class="text-right">Valor Total</TableHead>
                                    <TableHead class="text-right">Nº Negócios</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="product in products" :key="product.product_name">
                                    <TableCell class="font-medium">
                                        {{ product.product_name }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        {{ product.total_quantity }}
                                    </TableCell>
                                    <TableCell class="text-right font-semibold text-green-600">
                                        {{ formatCurrency(product.total_value) }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        {{ product.deal_count }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="outline" size="sm" @click="viewProduct(product.product_name)">
                                            Ver Detalhes
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <p v-else class="text-gray-500 text-center py-8">
                            Nenhum produto encontrado.
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>