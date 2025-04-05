<script setup lang="ts">
import { ref, watch } from 'vue';
import BaseModal from './BaseModal.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  modelValue: boolean
}>();
const emit = defineEmits(['update:modelValue', 'submit']);

// local state
const visible = ref(props.modelValue);
const amount = ref<number | null>(null);

// sync prop <-> local ref
watch(() => props.modelValue, (val) => visible.value = val);
watch(visible, (val) => emit('update:modelValue', val));

const submit = () => {
  emit('submit', amount.value);
  visible.value = false;
};

</script>

<template>
    <BaseModal v-model="visible" title="Deposit Funds">
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
            <Button @click="visible = false" class="bg-gray-300 px-4 py-2 text-black hover:bg-gray-400">
                Cancel
            </Button>
        </template>
    </BaseModal>
</template>
