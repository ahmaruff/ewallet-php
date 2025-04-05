<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" :class="className" v-bind="$attrs">
        <path
            fill="currentColor"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M17.91 15A1.92 1.92 0 0 0 16 16.91v4.18A1.92 1.92 0 0 0 17.91 23H26a4 4 0 0 0 1-7.86V11.4A2.44 2.44 0 0 0 24.53 9H22V5.73A1.74 1.74 0 0 0 20.27 4H11a1 1 0 0 0 0 2h9v3H5.5a1.5 1.5 0 0 1 0-3H7a1 1 0 0 0 0-2H5.5a3.5 3.5 0 0 0-3.33 2.47A1 1 0 0 0 2 7v19.6A2.43 2.43 0 0 0 4.5 29h20.06A2.44 2.44 0 0 0 27 26.56v-.6a1 1 0 0 0-1-1 1 1 0 0 0-1 1v.56a.44.44 0 0 1-.47.4H4.5a.44.44 0 0 1-.5-.32V11h20.53a.44.44 0 0 1 .47.4V15zM28 19a2 2 0 0 1-2 2h-8v-4h8a2 2 0 0 1 2 2z"/>
    </svg>
</template>
