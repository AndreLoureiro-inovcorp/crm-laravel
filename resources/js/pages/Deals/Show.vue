<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
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
import { Textarea } from '@/components/ui/textarea'
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

interface DealProposal {
    id: number
    file_path: string
    sent_at?: string | null
    sent_by?: number | null
}

interface Deal {
    id: number
    title: string
    value: number
    stage: string
    probability: number
    expected_close_date?: string | null
    entity?: (Entity & { email?: string | null }) | null
    person?: Person | null
    owner?: User | null
    products?: DealProduct[]
    activities?: DealActivity[]
    proposal?: DealProposal | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { deal } = defineProps<{
    deal: Deal
}>()

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */

const proposalFile = ref<File | null>(null)
const showEmailModal = ref(false)

const emailForm = useForm({
    email_body: `Exmo(a) Sr(a),

Segue em anexo a proposta solicitada para o negócio "${deal.title}".

Ficamos ao dispor para qualquer esclarecimento adicional.

Cumprimentos,
${deal.owner?.name ?? 'Equipa Comercial'}`,
})

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

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        proposalFile.value = target.files[0]
    }
}

function uploadProposal() {
    if (!proposalFile.value) return

    const formData = new FormData()
    formData.append('proposal', proposalFile.value)

    router.post(`/deals/${deal.id}/proposal/upload`, formData, {
        preserveScroll: true,
        onSuccess: () => {
            proposalFile.value = null
        },
    })
}

function openEmailModal() {
    if (!deal.proposal) return
    showEmailModal.value = true
}

function sendProposal() {
    emailForm.post(`/deals/${deal.id}/proposal/send`, {
        preserveScroll: true,
        onSuccess: () => {
            showEmailModal.value = false
        },
    })
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
                                    {{
                                        deal.expected_close_date
                                            ? new Date(deal.expected_close_date).toLocaleDateString('pt-PT')
                                            : '-'
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Proposta -->
                        <div class="mb-8 border-t pt-6">
                            <h2 class="text-xl font-bold mb-4">Proposta</h2>

                            <div v-if="deal.proposal" class="bg-gray-50 p-6 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium">
                                            {{ deal.proposal.file_path.split('/').pop() }}
                                        </p>
                                    </div>

                                    <Button @click="openEmailModal">
                                        Enviar ao Cliente
                                    </Button>
                                </div>
                            </div>

                            <div v-else class="bg-gray-50 p-6 rounded-lg">
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-600">Nenhuma proposta carregada.</p>

                                    <div>
                                        <Label for="proposal-file">Carregar Proposta (PDF, DOC, DOCX - Max 10MB)</Label>
                                        <Input id="proposal-file" type="file" accept=".pdf,.doc,.docx"
                                            @change="handleFileChange" class="mt-2" />
                                    </div>

                                    <Button @click="uploadProposal" :disabled="!proposalFile">
                                        Carregar Proposta
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Email Modal -->
                        <Dialog v-model:open="showEmailModal">
                            <DialogContent class="max-w-2xl">
                                <DialogHeader>
                                    <DialogTitle>Enviar Proposta ao Cliente</DialogTitle>
                                    <DialogDescription>
                                        Personalize o email antes de enviar a proposta.
                                    </DialogDescription>
                                </DialogHeader>

                                <div class="space-y-4 py-4">
                                    <div>
                                        <Label>Destinatário</Label>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ deal.person?.email ?? deal.entity?.email ?? 'Email não configurado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <Label for="email_body">Mensagem</Label>
                                        <Textarea id="email_body" v-model="emailForm.email_body" :rows="10"
                                            class="mt-2 font-mono text-sm" />
                                        <p v-if="emailForm.errors.email_body" class="text-red-600 text-sm mt-1">
                                            {{ emailForm.errors.email_body }}
                                        </p>
                                    </div>
                                </div>

                                <DialogFooter>
                                    <Button variant="outline" @click="showEmailModal = false">
                                        Cancelar
                                    </Button>
                                    <Button @click="sendProposal" :disabled="emailForm.processing">
                                        Enviar Proposta
                                    </Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>

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
                        <div>
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

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>