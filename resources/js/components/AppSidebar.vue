<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Building2,
    Users,
    Briefcase,
    Calendar,
    BarChart3,
    Zap,
    FileText,
    MessageSquare,
} from 'lucide-vue-next'

import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'
import { dashboard } from '@/routes'
import { type NavItem } from '@/types'
import AppLogo from './AppLogo.vue'

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Entidades',
        href: '/entities',
        icon: Building2,
    },
    {
        title: 'Pessoas',
        href: '/people',
        icon: Users,
    },
    {
        title: 'Negócios',
        href: '/deals',
        icon: Briefcase,
    },
    {
        title: 'Calendário',
        href: '/calendar',
        icon: Calendar,
    },
    {
        title: 'Estatísticas Produtos',
        href: '/products/stats',
        icon: BarChart3,
    },
    {
        title: 'Automatizações',
        href: '/automations',
        icon: Zap,
    },
    {
        title: 'Formulários',
        href: '/forms',
        icon: FileText,
    },
    {
        title: 'Chat Inteligente',
        href: '/chat',
        icon: MessageSquare,
    },
]

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
]
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
