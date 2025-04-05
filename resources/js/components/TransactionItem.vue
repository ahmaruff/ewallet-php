<script setup lang="ts">
defineProps<{
    transaction: {
        id: string
        type: string
        amount: number | string
        created_at: string
        description?: string
    }
}>()

const formatCurrency = (value: number | string) => {
    const amount = typeof value === 'string' ? parseFloat(value) : value
    return amount.toLocaleString('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    })
}

const getTypeBadgeClass = (type: string) => {
    switch (type) {
        case 'deposit':
            return 'bg-green-100 text-green-800'
        case 'withdrawal':
            return 'bg-red-100 text-red-800'
        case 'transfer_in':
            return 'bg-blue-100 text-blue-800'
        case 'transfer_out':
            return 'bg-yellow-100 text-yellow-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}
</script>

<template>
    <li class="p-4 border rounded-md shadow-sm">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ new Date(transaction.created_at).toLocaleString('id-ID') }}</span>
            <span
                class="px-2 py-1 rounded text-xs font-medium"
                :class="getTypeBadgeClass(transaction.type)"
            >
                {{ transaction.type }}
            </span>
        </div>
        <div class="mt-2 text-lg font-bold">{{ formatCurrency(transaction.amount) }}</div>
        <div class="text-sm text-gray-500">{{ transaction.description || '-' }}</div>
    </li>
</template>
