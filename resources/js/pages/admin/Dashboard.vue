<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem} from '@/types';
import { Head} from '@inertiajs/vue3';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import TransactionChart from '@/components/TransactionChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin Dashboard',
    href: '/admin/dashboard',
  },
];

const props = defineProps({
  user_count: Number,
  total_transaction: Number,
  recent_transactions: Array,
  average_transaction: Number,
});

const formatCurrency = (value: number | string) => {
    const amount = typeof value === 'string' ? parseFloat(value) : value
    return amount.toLocaleString('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    })
}

</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Stats Section -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Users Card -->
        <Card>
          <CardHeader>
            <CardTitle>Total Users</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-lg">{{ props.user_count }}</p>
          </CardContent>
        </Card>

        <!-- Total Transactions Card -->
        <Card>
          <CardHeader>
            <CardTitle>Total Transactions</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-lg">{{ props.total_transaction ? formatCurrency(props.total_transaction) : formatCurrency(0) }}</p>
          </CardContent>
        </Card>

        <!-- Average Transaction Card -->
        <Card>
          <CardHeader>
            <CardTitle>Average Transaction</CardTitle>
          </CardHeader>
          <CardContent>
            <p class="text-lg">{{ props.average_transaction ? formatCurrency(props.average_transaction) : formatCurrency(0) }}</p>
          </CardContent>
        </Card>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Transaction from last 30 days</CardTitle>
        </CardHeader>
        <CardContent>
          <TransactionChart />
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
