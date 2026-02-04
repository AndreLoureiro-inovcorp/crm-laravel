<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
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

interface AutomationRule {
    id: number
    name: string
    trigger_type: string
    trigger_value: number
    action_type: string
    is_active: boolean
    created_at: string
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { rules } = defineProps<{
    rules: AutomationRule[]
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const showModal = ref(false)

const form = useForm({
    name: '',
    trigger_type: 'no_activity',
    trigger_value: '',
    action_type: 'create_task',
})

/* -------------------------------------------------------------------------- */
/* HELPERS */
/* -------------------------------------------------------------------------- */

function getTriggerLabel(rule: AutomationRule): string {
    if (rule.trigger_type === 'no_activity') {
        return `Sem atividade há ${rule.trigger_value} dias`
    }
    return `Parado em etapa há ${rule.trigger_value} dias`
}

function getActionLabel(actionType: string): string {
    if (actionType === 'create_task') return 'Criar tarefa'
    return 'Enviar notificação'
}

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function openModal() {
    showModal.value = true
}

function createAutomation() {
    form.post('/automations', {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}

function toggleAutomation(ruleId: number) {
    router.patch(`/automations/${ruleId}/toggle`, {}, {
        preserveScroll: true,
    })
}

function deleteAutomation(ruleId: number) {
    if (confirm('Tem a certeza que deseja eliminar esta automação?')) {
        router.delete(`/automations/${ruleId}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AppLayout>

        <Head title="Automações" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Fluxos de Automatização</h1>
                    <Button @click="openModal">+ Nova Automação</Button>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <Table v-if="rules.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Gatilho</TableHead>
                                    <TableHead>Ação</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead class="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow v-for="rule in rules" :key="rule.id">
                                    <TableCell class="font-medium">
                                        {{ rule.name }}
                                    </TableCell>

                                    <TableCell>
                                        {{ getTriggerLabel(rule) }}
                                    </TableCell>

                                    <TableCell>
                                        {{ getActionLabel(rule.action_type) }}
                                    </TableCell>

                                    <TableCell>
                                        <Badge :variant="rule.is_active ? 'default' : 'secondary'">
                                            {{ rule.is_active ? 'Ativa' : 'Inativa' }}
                                        </Badge>
                                    </TableCell>

                                    <TableCell class="text-right space-x-2">
                                        <Button variant="outline" size="sm" @click="toggleAutomation(rule.id)">
                                            {{ rule.is_active ? 'Desativar' : 'Ativar' }}
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="deleteAutomation(rule.id)">
                                            Eliminar
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <p v-else class="text-gray-500 text-center py-8">
                            Nenhuma automação criada. Crie a sua primeira automação!
                        </p>

                    </div>
                </div>

            </div>
        </div>

        <!-- Create Modal -->
        <Dialog v-model:open="showModal">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Nova Automação</DialogTitle>
                    <DialogDescription>
                        Configure uma regra de automação para o seu CRM.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 py-4">
                    <div>
                        <Label for="name">Nome da Automação *</Label>
                        <Input id="name" v-model="form.name" type="text" class="mt-1"
                            placeholder="Ex: Follow-up após 7 dias" />
                        <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <Label for="trigger_type">Gatilho *</Label>
                        <select id="trigger_type" v-model="form.trigger_type"
                            class="mt-1 w-full border rounded px-3 py-2">
                            <option value="no_activity">Sem atividade há X dias</option>
                            <option value="stage_stuck">Parado em etapa há X dias</option>
                        </select>
                        <p v-if="form.errors.trigger_type" class="text-red-600 text-sm mt-1">
                            {{ form.errors.trigger_type }}
                        </p>
                    </div>

                    <div>
                        <Label for="trigger_value">Número de Dias *</Label>
                        <Input id="trigger_value" v-model="form.trigger_value" type="number" min="1" class="mt-1" />
                        <p v-if="form.errors.trigger_value" class="text-red-600 text-sm mt-1">
                            {{ form.errors.trigger_value }}
                        </p>
                    </div>

                    <div>
                        <Label for="action_type">Ação *</Label>
                        <select id="action_type" v-model="form.action_type"
                            class="mt-1 w-full border rounded px-3 py-2">
                            <option value="create_task">Criar tarefa de follow-up</option>
                            <option value="send_notification">Enviar notificação</option>
                        </select>
                        <p v-if="form.errors.action_type" class="text-red-600 text-sm mt-1">
                            {{ form.errors.action_type }}
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showModal = false">
                        Cancelar
                    </Button>
                    <Button @click="createAutomation" :disabled="form.processing">
                        Criar Automação
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>