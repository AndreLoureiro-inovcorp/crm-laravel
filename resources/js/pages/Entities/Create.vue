<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* FORM */
/* -------------------------------------------------------------------------- */

const form = useForm({
    name: '',
    vat: '',
    email: '',
    phone: '',
    address: '',
    status: 'active' as 'active' | 'inactive',
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function submit() {
    form.post('/entities', {
        preserveScroll: true,
    })
}

function cancel() {
    router.visit('/entities')
}
</script>

<template>
    <AppLayout>

        <Head title="Criar Entidade" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold mb-6">Criar Entidade</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nome -->
                            <div>
                                <Label for="name">Nome *</Label>
                                <Input id="name" v-model="form.name" type="text" required class="mt-1" />
                                <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- NIF -->
                            <div>
                                <Label for="vat">NIF</Label>
                                <Input id="vat" v-model="form.vat" type="text" class="mt-1" />
                                <div v-if="form.errors.vat" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.vat }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" class="mt-1" />
                                <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div>
                                <Label for="phone">Telefone</Label>
                                <Input id="phone" v-model="form.phone" type="text" class="mt-1" />
                                <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <!-- Morada -->
                            <div>
                                <Label for="address">Morada</Label>
                                <Textarea id="address" v-model="form.address" :rows="3" class="mt-1" />
                                <div v-if="form.errors.address" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <Label for="status">Estado *</Label>
                                <select id="status" v-model="form.status" class="mt-1 w-full border rounded px-3 py-2"
                                    required>
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.status }}
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" @click="cancel">
                                    Cancelar
                                </Button>

                                <Button type="submit" :disabled="form.processing">
                                    Criar Entidade
                                </Button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>