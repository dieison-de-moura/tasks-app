<script setup lang="ts">
import { ref, watch, computed, type PropType } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  class: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});
const emits = defineEmits(['update:modelValue']);

const localValue = ref(props.modelValue);
watch(() => props.modelValue, v => localValue.value = v);
watch(localValue, v => emits('update:modelValue', v));
</script>

<template>
  <div class="relative">
    <select
      v-model="localValue"
      :disabled="props.disabled"
      :class="cn(
        'peer block w-full appearance-none rounded-md border border-input bg-background px-3 py-2 pr-8 text-sm shadow-xs transition-colors focus:border-ring focus:outline-none focus:ring-2 focus:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50',
        props.class
      )"
    >
      <slot />
    </select>
    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground">
      <svg width="20" height="20" fill="none" viewBox="0 0 20 20"><path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </span>
  </div>
</template> 