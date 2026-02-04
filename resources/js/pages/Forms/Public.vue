<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, onMounted } from 'vue'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface Field {
    id: string
    label: string
    type: string
    required: boolean
}

interface PublicForm {
    id: number
    name: string
    fields: Field[]
    confirmation_message: string
}

interface PageProps {
    flash?: {
        success?: string
    }
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { form, recaptchaSiteKey } = defineProps<{
    form: PublicForm
    recaptchaSiteKey: string
}>()

/* -------------------------------------------------------------------------- */
/* PAGE */
/* -------------------------------------------------------------------------- */

const page = usePage()

const successMessage = computed(() => {
    return (page.props.flash as { success?: string } | undefined)?.success
})

/* -------------------------------------------------------------------------- */
/* STATE */
/* -------------------------------------------------------------------------- */
const initialData: Record<string, string> = {}

form.fields.forEach((field) => {
    initialData[field.id] = ''
})

const formData = useForm(initialData)

/* -------------------------------------------------------------------------- */
/* COMPUTED */
/* -------------------------------------------------------------------------- */

const hasRecaptcha = computed(() => {
    return recaptchaSiteKey && recaptchaSiteKey !== 'your_site_key_here'
})

/* -------------------------------------------------------------------------- */
/* LIFECYCLE */
/* -------------------------------------------------------------------------- */

onMounted(() => {
    if (hasRecaptcha.value) {
        const script = document.createElement('script')
        script.src = 'https://www.google.com/recaptcha/api.js'
        script.async = true
        script.defer = true
        document.head.appendChild(script)
    }
})

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function submit() {
    formData.post(`/form/${form.id}/submit`)
}
</script>

<template>

    <Head :title="form.name" />

    <div class="min-h-screen bg-gray-100 py-12 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">

                <h1 class="text-3xl font-bold text-center mb-8">
                    {{ form.name }}
                </h1>

                <form @submit.prevent="submit" class="space-y-6">

                    <div v-for="field in form.fields" :key="field.id" class="space-y-2">
                        <Label :for="field.id" class="text-base font-medium">
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-600">*</span>
                        </Label>

                        <Textarea v-if="field.type === 'textarea'" :id="field.id" v-model="formData[field.id]"
                            :required="field.required" :rows="4" class="w-full" />

                        <Input v-else :id="field.id" v-model="formData[field.id]" :type="field.type"
                            :required="field.required" class="w-full" />

                        <p v-if="formData.errors[field.id]" class="text-red-600 text-sm">
                            {{ formData.errors[field.id] }}
                        </p>
                    </div>

                    <div v-if="hasRecaptcha" class="flex justify-center pt-4">
                        <div class="g-recaptcha" :data-sitekey="recaptchaSiteKey"></div>
                    </div>

                    <p v-if="formData.errors.recaptcha" class="text-red-600 text-sm text-center">
                        {{ formData.errors.recaptcha }}
                    </p>

                    <div class="flex justify-center pt-4">
                        <Button type="submit" :disabled="formData.processing" class="px-12 py-3 text-lg">
                            Enviar
                        </Button>
                    </div>

                </form>

                <div v-if="successMessage" class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800 text-center font-medium">
                        {{ successMessage }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>
