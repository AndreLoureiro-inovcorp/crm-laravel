<script setup lang="ts">
import type {
    CalendarOptions,
    EventClickArg,
    DateSelectArg,
    EventInput,
} from '@fullcalendar/core'
import ptLocale from '@fullcalendar/core/locales/pt'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import FullCalendar from '@fullcalendar/vue3'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'



import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'

/* -------------------------------------------------------------------------- */
/* TYPES */
/* -------------------------------------------------------------------------- */

interface CalendarEvent {
    id: number
    title: string
    start: string
    end: string | null
    description?: string | null
    location?: string | null
    owner?: string | null
    eventable_type?: string | null
    eventable_name?: string | null
}

/* -------------------------------------------------------------------------- */
/* PROPS */
/* -------------------------------------------------------------------------- */

const { events } = defineProps<{
    events: CalendarEvent[]
}>()

/* -------------------------------------------------------------------------- */
/* MAP EVENTS FOR FULLCALENDAR (OBRIGATÓRIO) */
/* -------------------------------------------------------------------------- */

const calendarEvents = computed<EventInput[]>(() =>
    events.map((event) => ({
        id: event.id.toString(),
        title: event.title,
        start: event.start,
        end: event.end ?? undefined,
        extendedProps: {
            description: event.description,
            location: event.location,
            owner: event.owner,
            eventable_type: event.eventable_type,
            eventable_name: event.eventable_name,
        },
    }))
)

/* -------------------------------------------------------------------------- */
/* ACTIONS */
/* -------------------------------------------------------------------------- */

function handleEventClick(info: EventClickArg) {
    router.visit(`/calendar/${info.event.id}`)
}

function handleDateSelect(info: DateSelectArg) {
    router.visit(`/calendar/create?start=${info.startStr}`)
}

/* -------------------------------------------------------------------------- */
/* CALENDAR OPTIONS (TIPADO) */
/* -------------------------------------------------------------------------- */

const calendarOptions = computed<CalendarOptions>(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: ptLocale,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    buttonText: {
        today: 'Hoje',
        month: 'Mês',
        week: 'Semana',
        day: 'Dia',
    },
    events: calendarEvents.value,
    editable: false,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    height: 'auto',
    eventClick: handleEventClick,
    select: handleDateSelect,
}))
</script>

<template>
    <AppLayout>
        <Head title="Calendário" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Calendário</h1>
                    <Link href="/calendar/create">
                        <Button>Criar Evento</Button>
                    </Link>
                </div>

                <!-- Calendar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <FullCalendar :options="calendarOptions" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style>
:root {
    --fc-border-color: #e5e7eb;
    --fc-button-bg-color: #3b82f6;
    --fc-button-border-color: #3b82f6;
    --fc-button-hover-bg-color: #2563eb;
    --fc-button-hover-border-color: #2563eb;
    --fc-button-active-bg-color: #1d4ed8;
    --fc-button-active-border-color: #1d4ed8;
    --fc-event-bg-color: #3b82f6;
    --fc-event-border-color: #3b82f6;
    --fc-today-bg-color: #dbeafe;
}

.fc {
    font-family: inherit;
}

.fc .fc-button {
    text-transform: capitalize;
}
</style>

