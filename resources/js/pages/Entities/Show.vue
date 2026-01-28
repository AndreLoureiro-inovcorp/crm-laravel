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

interface Person {
  id: number
  name: string
  email?: string | null
  phone?: string | null
  position?: string | null
}

interface Deal {
  id: number
  title: string
  value: number
  stage: string
  probability: number
}

interface Entity {
  id: number
  name: string
  vat?: string | null
  email?: string | null
  phone?: string | null
  address?: string | null
  status: 'active' | 'inactive'
  people?: Person[]
  deals?: Deal[]
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { entity } = defineProps<{
  entity: Entity
}>()
</script>

<template>
  <AppLayout>

    <Head :title="entity.name" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h1 class="text-2xl font-bold">{{ entity.name }}</h1>
                <Badge :variant="entity.status === 'active' ? 'default' : 'secondary'" class="mt-2">
                  {{ entity.status === 'active' ? 'Ativo' : 'Inativo' }}
                </Badge>
              </div>

              <div class="flex gap-2">
                <Link :href="`/entities/${entity.id}/edit`">
                  <Button>Editar</Button>
                </Link>

                <Link href="/entities">
                  <Button variant="outline">Voltar</Button>
                </Link>
              </div>
            </div>

            <!-- Informação da Entidade -->
            <div class="grid grid-cols-2 gap-6 mb-8">
              <div>
                <h3 class="text-sm font-medium text-gray-500">NIF</h3>
                <p class="mt-1 text-sm text-gray-900">
                  {{ entity.vat ?? '-' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Email</h3>
                <p class="mt-1 text-sm text-gray-900">
                  {{ entity.email ?? '-' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Telefone</h3>
                <p class="mt-1 text-sm text-gray-900">
                  {{ entity.phone ?? '-' }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Morada</h3>
                <p class="mt-1 text-sm text-gray-900">
                  {{ entity.address ?? '-' }}
                </p>
              </div>
            </div>

            <!-- Pessoas Associadas -->
            <div class="mb-8">
              <h2 class="text-xl font-bold mb-4">Pessoas Associadas</h2>

              <Table v-if="entity.people?.length">
                <TableHeader>
                  <TableRow>
                    <TableHead>Nome</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Telefone</TableHead>
                    <TableHead>Cargo</TableHead>
                  </TableRow>
                </TableHeader>

                <TableBody>
                  <TableRow v-for="person in entity.people" :key="person.id">
                    <TableCell class="font-medium">
                      {{ person.name }}
                    </TableCell>
                    <TableCell>{{ person.email ?? '-' }}</TableCell>
                    <TableCell>{{ person.phone ?? '-' }}</TableCell>
                    <TableCell>{{ person.position ?? '-' }}</TableCell>
                  </TableRow>
                </TableBody>
              </Table>

              <p v-else class="text-gray-500 text-sm">
                Nenhuma pessoa associada.
              </p>
            </div>

            <!-- Histórico de Negócios -->
            <div>
              <h2 class="text-xl font-bold mb-4">Histórico de Negócios</h2>

              <Table v-if="entity.deals?.length">
                <TableHeader>
                  <TableRow>
                    <TableHead>Título</TableHead>
                    <TableHead>Valor</TableHead>
                    <TableHead>Etapa</TableHead>
                    <TableHead>Probabilidade</TableHead>
                  </TableRow>
                </TableHeader>

                <TableBody>
                  <TableRow v-for="deal in entity.deals" :key="deal.id">
                    <TableCell class="font-medium">
                      {{ deal.title }}
                    </TableCell>
                    <TableCell>{{ deal.value }}€</TableCell>
                    <TableCell>{{ deal.stage }}</TableCell>
                    <TableCell>{{ deal.probability }}%</TableCell>
                  </TableRow>
                </TableBody>
              </Table>

              <p v-else class="text-gray-500 text-sm">
                Nenhum negócio registado.
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
