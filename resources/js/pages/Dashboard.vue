<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {Card, CardHeader, CardContent, CardTitle } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { WalletMinimal, HandCoins } from 'lucide-vue-next';
import DepositModal from '@/components/modal/DepositModal.vue';

const props = defineProps<{
    balance: number
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const showDeposit = ref(false);

const handleDeposit = (amount: number) => {
  console.log('Deposit submitted with amount:', amount);
  // Kirim ke server via Inertia/Axios
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            Balance
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <h1 class="text-4xl font-bold">{{ props.balance.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }) }}</h1>
                    </CardContent>
                </Card>
                <div class="flex flex-col justify-center items-start gap-4">
                    <Button variant="outline" @click="showDeposit = true">
                        <WalletMinimal /> Deposit
                    </Button>
                    <Button variant="secondary">
                        <HandCoins /> Withdraw
                    </Button>
                </div>
                <DepositModal v-model="showDeposit" @submit="handleDeposit" />
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                
            </div>
        </div>
    </AppLayout>
</template>
