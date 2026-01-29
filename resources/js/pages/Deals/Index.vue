<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
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
    value: number
    stage: string
    probability: number
    expected_close_date?: string | null
    entity?: Entity | null
    person?: Person | null
    owner?: User | null
}

interface DealStage {
    id: number
    name: string
    order: number
    color: string
}

interface Filters {
    owner_id?: number
    min_value?: number
    max_value?: number
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { stages, dealsByStage, filters } = defineProps<{
    stages: DealStage[]
    dealsByStage: Record<number, Deal[]>
    filters?: Filters
}>()

/* -------------------------------------------------------------------------- */
/* STATE (Inputs SEMPRE string com shadcn) */
/* -------------------------------------------------------------------------- */

const ownerId = ref<number | null>(filters?.owner_id ?? null)
const minValue = ref<string>(filters?.min_value?.toString() ?? '')
const maxValue = ref<string>(filters?.max_value?.toString() ?? '')

const draggedDeal = ref<Deal | null>(null)

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function applyFilters() {
    router.get(
        '/deals',
        {
            owner_id: ownerId.value,
            min_value: minValue.value ? Number(minValue.value) : undefined,
            max_value: maxValue.value ? Number(maxValue.value) : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

function onDragStart(deal: Deal) {
    draggedDeal.value = deal
}

function onDragOver(event: DragEvent) {
    event.preventDefault()
}

function onDrop(event: DragEvent, stageName: string) {
    event.preventDefault()

    if (!draggedDeal.value) return

    if (draggedDeal.value.stage !== stageName) {
        router.patch(
            `/deals/${draggedDeal.value.id}/stage`,
            { stage: stageName },
            {
                preserveScroll: true,
                onSuccess: () => {
                    draggedDeal.value = null
                },
            }
        )
    }
}

function getTotalValue(stageId: number): number {
    return dealsByStage[stageId]?.reduce((sum, deal) => sum + Number(deal.value), 0) ?? 0
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value)
}
</script>

<template>
    <AppLayout>

        <Head title="Negócios" />

        <div class="py-12">
            <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Negócios</h1>
                    <Link href="/deals/create">
                        <Button>Criar Negócio</Button>
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex gap-4">
                        <Input v-model="minValue" type="number" placeholder="Valor mínimo" class="max-w-xs" />
                        <Input v-model="maxValue" type="number" placeholder="Valor máximo" class="max-w-xs" />
                        <Button @click="applyFilters" variant="outline">
                            Filtrar
                        </Button>
                    </div>
                </div>

                <!-- Kanban Board -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div v-for="stage in stages" :key="stage.id" class="bg-gray-50 rounded-lg p-4 min-h-[600px]"
                        @dragover="onDragOver" @drop="onDrop($event, stage.name)">
                        <!-- Stage Header -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: stage.color }" />
                                    <h3 class="font-semibold">{{ stage.name }}</h3>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ dealsByStage[stage.id]?.length ?? 0 }}
                                </span>
                            </div>
                            <p class="text-xs font-medium text-gray-600">
                                {{ formatCurrency(getTotalValue(stage.id)) }}
                            </p>
                        </div>

                        <!-- Deals -->
                        <div class="space-y-3">
                            <div v-for="deal in dealsByStage[stage.id]" :key="deal.id" draggable="true"
                                @dragstart="onDragStart(deal)"
                                class="bg-white rounded-lg p-4 shadow-sm cursor-move hover:shadow-md transition-shadow">
                                <Link :href="`/deals/${deal.id}`">
                                    <h4 class="font-medium text-sm mb-2 hover:text-blue-600">
                                        {{ deal.title }}
                                    </h4>
                                </Link>

                                <div class="space-y-1 text-xs text-gray-600">
                                    <p class="font-semibold text-green-600">
                                        {{ formatCurrency(deal.value) }}
                                    </p>

                                    <p v-if="deal.entity">🏢 {{ deal.entity.name }}</p>
                                    <p v-if="deal.person">👤 {{ deal.person.name }}</p>
                                    <p v-if="deal.owner" class="text-gray-500">
                                        Responsável: {{ deal.owner.name }}
                                    </p>

                                    <p class="text-gray-500">
                                        Probabilidade: {{ deal.probability }}%
                                    </p>

                                    <p v-if="deal.expected_close_date" class="text-gray-500">
                                        Fecho previsto:
                                        {{ new Date(deal.expected_close_date).toLocaleDateString('pt-PT') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
[draggable="true"] {
    touch-action: none;
}
</style>
