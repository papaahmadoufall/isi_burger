<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/burgers/Layout.vue';
import { Button } from '@/components/ui/button';
import ListCommands from './ListCommands.vue';

</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Command Management', href: '/dashboard/commands' }]">
        <Head title="Command Management" />

        <SettingsLayout>
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Commands</h1>
            </div>
        </SettingsLayout>
        <ListCommands></ListCommands>
    </AppLayout>
</template>
