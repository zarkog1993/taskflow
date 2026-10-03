<template>
    <div ref="root" class="relative inline-block text-xs">
        <button
            type="button"
            :aria-label="`Mesec: ${selectedLabel}`"
            :aria-expanded="open"
            aria-haspopup="true"
            @click="toggle"
            class="flex w-full min-w-36 items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-gray-900 dark:text-white hover:border-gray-500 focus:outline-none focus:border-indigo-500"
        >
            <span>{{ selectedLabel }}</span>
            <span aria-hidden="true" class="text-gray-600 dark:text-gray-400">▾</span>
        </button>

        <div
            v-if="open"
            class="absolute right-0 top-full z-50 mt-2 w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 text-gray-900 dark:text-white shadow-2xl"
            role="group"
            aria-label="Izaberite mesec"
        >
            <div class="mb-3 flex items-center justify-between">
                <button type="button" aria-label="Prethodna godina" :disabled="year <= 1" @click="year--" class="rounded px-2 py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 disabled:opacity-40">‹</button>
                <span class="font-semibold">{{ year }}</span>
                <button type="button" aria-label="Sledeća godina" :disabled="year >= 9999" @click="year++" class="rounded px-2 py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 disabled:opacity-40">›</button>
            </div>
            <div class="grid grid-cols-3 gap-1">
                <button
                    v-for="(month, index) in MONTHS"
                    :key="month"
                    type="button"
                    :aria-label="`${month} ${year}`"
                    :aria-pressed="year === selectedYear && index + 1 === selectedMonth"
                    @click="selectMonth(index + 1)"
                    :class="year === selectedYear && index + 1 === selectedMonth ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="rounded-md px-1 py-2 text-center font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    {{ month.slice(0, 3) }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

const MONTHS = ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar']
const props = defineProps({ modelValue: { type: String, required: true } })
const emit = defineEmits(['update:modelValue'])

const root = ref(null)
const open = ref(false)
const selectedYear = computed(() => Number(props.modelValue.slice(0, 4)))
const selectedMonth = computed(() => Number(props.modelValue.slice(5, 7)))
const selectedLabel = computed(() => `${MONTHS[selectedMonth.value - 1]} ${selectedYear.value}`)
const year = ref(selectedYear.value)

function toggle() {
    if (!open.value) year.value = selectedYear.value
    open.value = !open.value
}

function selectMonth(month) {
    emit('update:modelValue', `${String(year.value).padStart(4, '0')}-${String(month).padStart(2, '0')}`)
    open.value = false
}

function onPointerDown(event) {
    if (open.value && !root.value?.contains(event.target)) open.value = false
}

function onKeyDown(event) {
    if (event.key === 'Escape' && open.value) {
        open.value = false
        root.value?.querySelector('button')?.focus()
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', onPointerDown)
    document.addEventListener('keydown', onKeyDown)
})

onUnmounted(() => {
    document.removeEventListener('pointerdown', onPointerDown)
    document.removeEventListener('keydown', onKeyDown)
})
</script>