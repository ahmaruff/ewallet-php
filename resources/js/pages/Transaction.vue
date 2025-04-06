<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Transaction, PaginationInfo } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { Card, CardHeader, CardContent, CardTitle } from '../components/ui/card';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import TransactionItem from '@/components/TransactionItem.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Transaction',
    href: '/transaction',
  },
];

interface PageProps {
  transactions: Transaction[]
  pagination: PaginationInfo
}

const page = usePage<PageProps>()

const transactions = computed(() => page.props.transactions || [])
const pagination = computed(() => page.props.pagination)

const goTo = (url: string | null) => {
  if (url) {
    router.visit(url)
  }
}

const today = new Date();
const endDate = ref(today.toISOString().slice(0, 10));
const startDate = ref(new Date(today.setDate(today.getDate() - 30)).toISOString().slice(0, 10));

const perPage = ref(pagination.value?.per_page || 10)

const applyFilters = () => {
  router.visit('/transactions', {
    method: 'get',
    data: {
      start_date: startDate.value,
      end_date: endDate.value,
      per_page: perPage.value,
    },
    preserveState: true,
    preserveScroll: true,
  })
}

</script>

<template>
  <Head title="Transaction History" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- filters -->
      <div class="flex flex-row gap-2 ml-auto">
        <div class="flex flex-col">
          <label class="text-sm font-medium mb-1">Start Date</label>
          <input
            type="date"
            v-model="startDate"
            class="rounded border px-3 py-2 w-40"
          />
        </div>
        <div class="flex flex-col">
          <label class="text-sm font-medium mb-1">End Date</label>
          <input
            type="date"
            v-model="endDate"
            class="rounded border px-3 py-2 w-40"
          />
        </div>
        <div class="flex flex-col">
          <label class="text-sm font-medium mb-1">Per Page</label>
          <input
            type="number"
            v-model="perPage"
            class="rounded border px-3 py-2 w-16"
          />
        </div>
        <div class="flex flex-col">
          <label class="text-sm font-medium mb-1 invisible">Apply</label>
          <button
            @click="applyFilters"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
            Apply
          </button>
        </div>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Transaction History</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="transactions.length === 0" class="text-gray-500">
            No transactions found.
          </div>

          <ul v-else class="space-y-3">
            <TransactionItem
              v-for="transaction in transactions"
              :key="transaction.id"
              :transaction="transaction"
            />
          </ul>
        </CardContent>
        <CardFooter>
          <div class="flex flex-col md:flex-row gap-4 w-full">
            <!-- nav -->
            <div class="flex flex-end items-end w-full">
              <div class="flex flex-row justify-end gap-2 items-center w-full">
                <button
                  @click="goTo(pagination.previous_page_url)"
                  :disabled="!pagination.previous_page_url"
                  class="px-4 py-2 bg-gray-200 text-sm rounded disabled:opacity-50"
                >
                  Previous
                </button>
                <span class="text-sm text-gray-700">
                  Page {{ pagination.current_page }} of {{ pagination.total_pages }}
                </span>
                <button
                  @click="goTo(pagination.next_page_url)"
                  :disabled="!pagination.next_page_url"
                  class="px-4 py-2 bg-gray-200 text-sm rounded disabled:opacity-50"
                >
                  Next
                </button>
              </div>
            </div>
          </div>        
        </CardFooter>
      </Card>
    </div>
  </AppLayout>
</template>
