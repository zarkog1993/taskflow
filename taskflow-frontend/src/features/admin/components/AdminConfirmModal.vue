<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="titleId"
        @click.self="$emit('close')"
    >
        <div class="w-full max-w-md space-y-5 rounded-2xl border border-gray-700/80 bg-gray-800 p-6 text-center shadow-2xl">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-rose-800/80 bg-rose-950/80 text-2xl text-rose-400">
                {{ icon }}
            </div>

            <div class="space-y-2">
                <h3 :id="titleId" class="text-xl font-black text-white">{{ title }}</h3>
                <p class="text-sm leading-relaxed text-gray-400">{{ message }}</p>
            </div>

            <p v-if="error" class="rounded-xl border border-red-800 bg-red-950/60 p-3 text-xs text-red-300">
                {{ error }}
            </p>

            <div class="flex items-center justify-center gap-3 pt-2">
                <button
                    type="button"
                    :disabled="processing"
                    @click="$emit('close')"
                    class="w-full rounded-xl border border-gray-700 bg-gray-900 py-2.5 text-xs font-bold text-gray-300 transition hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="processing"
                    @click="$emit('confirm')"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white shadow-lg transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span v-if="processing" class="animate-spin">⏳</span>
                    <span>{{ processing ? processingLabel : confirmLabel }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const titleId = 'admin-confirm-modal-title'

defineProps({
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        required: true
    },
    confirmLabel: {
        type: String,
        default: 'Confirm'
    },
    processingLabel: {
        type: String,
        default: 'Processing...'
    },
    icon: {
        type: String,
        default: '⚠️'
    },
    processing: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: ''
    }
})

defineEmits(['close', 'confirm'])
</script>
