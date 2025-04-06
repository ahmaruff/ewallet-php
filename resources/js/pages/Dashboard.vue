<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Transaction } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardContent, CardTitle } from '../components/ui/card';
import { Button } from '../components/ui/button';
import { WalletMinimal, HandCoins } from 'lucide-vue-next';
import DepositModal from '@/components/modal/DepositModal.vue';
import WithdrawModal from '@/components/modal/WithdrawModal.vue';
import TransactionItem from '@/components/TransactionItem.vue'

const props = defineProps<{
  balance: number,
  recent_transactions: Transaction[],
}>();

const currentBalance = ref<number>(props.balance);

watch(() => props.balance, (newBalance) => {
  currentBalance.value = newBalance;
});

const formattedBalance = computed(() => {
  return currentBalance.value.toLocaleString('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  });
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

function getXSRFToken(): string | null {
    const name = 'XSRF-TOKEN=';
    const decoded = decodeURIComponent(document.cookie);
    const token = decoded.split('; ').find((row) => row.startsWith(name));
    return token ? token.substring(name.length) : null;
}

onMounted(() => {
    fetch('/sanctum/csrf-cookie')
        .then(() => console.log('CSRF cookie loaded'))
        .catch(() => console.warn('Failed to get CSRF cookie'));
});

const showDeposit = ref(false);
const showWithdraw = ref(false);

const handleDeposit = async (amount: number) => {
    console.log('Deposit submitted with amount:', amount);

    const token = getXSRFToken();

    if (!token) {
        console.error('CSRF token not found.');
        alert('Token CSRF tidak ditemukan');
        return;
    }

    try {
        const response = await fetch('/api/transactions/deposit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': token,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                amount: amount,
            }),
        });

        if (!response.ok) {
            const error = await response.json();
            console.error('Deposit failed:', error);
            alert(error.message || 'Terjadi kesalahan saat melakukan deposit.');
            return;
        }

        const data = await response.json();
        console.log('Deposit success:', data);

        currentBalance.value = data.data.balance;
        showDeposit.value = false;
    } catch (error) {
        console.error('Error submitting deposit:', error);
        alert('Terjadi kesalahan jaringan atau server tidak merespons.');
    }
};


const handleWithdraw = async (amount: number) => {
    console.log('Withdraw submitted with amount:', amount);

    const token = getXSRFToken();

    if (!token) {
        console.error('CSRF token not found.');
        alert('Token CSRF tidak ditemukan');
        return;
    }

    try {
        const response = await fetch('/api/transactions/withdraw', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': token,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                amount: amount,
            }),
        });

        if (!response.ok) {
            const error = await response.json();
            console.error('Withdraw failed:', error);
            alert(error.message || 'Terjadi kesalahan saat melakukan penarikan.');
            return;
        }

        const data = await response.json();
        console.log('Withdraw success:', data);

        currentBalance.value = data.data.balance;
        showWithdraw.value = false;
    } catch (error) {
        console.error('Error submitting withdraw:', error);
        alert('Terjadi kesalahan jaringan atau server tidak merespons.');
    }
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Balance</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <h1 class="text-4xl font-bold">
                            {{ formattedBalance }}
                        </h1>
                    </CardContent>
                </Card>

                <div class="flex flex-col justify-center items-start gap-4">
                    <Button variant="outline" @click="showDeposit = true">
                        <WalletMinimal /> Deposit
                    </Button>
                    <Button variant="secondary" @click="showWithdraw = true">
                        <HandCoins /> Withdraw
                    </Button>
                </div>
                <DepositModal v-model="showDeposit" @submit="handleDeposit" />
                <WithdrawModal v-model="showWithdraw" @submit="handleWithdraw" />
            </div>
            <!-- recent -->
            <Card>
                <CardHeader>
                <CardTitle>Recent Transaction</CardTitle>
                </CardHeader>
                <CardContent>
                <div v-if="props.recent_transactions?.length === 0" class="text-gray-500">
                    No transactions found.
                </div>

                <ul v-else class="space-y-3">
                    <TransactionItem
                    v-for="transaction in props.recent_transactions"
                    :key="transaction.id"
                    :transaction="transaction"
                    />
                </ul>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
