<script setup lang="ts">
import { ref, watch } from 'vue';
import BaseModal from './BaseModal.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  modelValue: boolean
}>();
const emit = defineEmits(['update:modelValue', 'submit']);

const amount = ref<number | null>(null);

// Reset amount ketika modal ditutup
watch(() => props.modelValue, (val) => {
  if (!val) amount.value = null;
});

const close = () => emit('update:modelValue', false);

const submit = () => {
  emit('submit', amount.value);
  close();
};
</script>

<template>
  <BaseModal :modelValue="props.modelValue" @update:modelValue="val => emit('update:modelValue', val)" title="Withdraw Funds">
    <label class="block mb-4">
      <span class="text-sm">Amount</span>
      <input
        v-model="amount"
        type="number"
        class="mt-1 w-full rounded-md border px-3 py-2"
        placeholder="Enter amount"
      />
    </label>

    <template #footer>  
      <Button @click="submit" class="bg-green-600 px-4 py-2 text-white hover:bg-green-700">
        Confirm
      </Button>
      <Button @click="close" class="bg-gray-300 px-4 py-2 text-black hover:bg-gray-400">
        Cancel
      </Button>
    </template>
  </BaseModal>
</template>
