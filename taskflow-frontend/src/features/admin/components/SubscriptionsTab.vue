<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
            <tr class="border-b border-gray-700 text-gray-400 uppercase font-bold text-[10px]">
                <th class="p-2">Korisnik / Klub</th>
                <th class="p-2">Paket</th>
                <th class="p-2">Status</th>
                <th class="p-2">Max Timova</th>
                <th class="p-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
            <tr v-for="sub in subscriptions" :key="sub.id" class="hover:bg-gray-900/40">
                <td class="p-2 font-bold text-white">{{ sub.user?.name }} ({{ sub.user?.club?.name || 'N/A' }})</td>
                <td class="p-2 text-emerald-400 font-bold uppercase">{{ sub.plan_type }}</td>
                <td class="p-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 text-[10px] uppercase font-bold">
                            {{ sub.status || 'Active' }}
                        </span>
                    <button
                        v-if="sub.status === 'pending'"
                        @click="$emit('approve', sub.id)"
                        class="ml-2 rounded bg-emerald-500 px-2 py-1 text-[10px] font-bold text-gray-950"
                    >
                        Approve
                    </button>
                </td>
                <td class="p-2 font-mono text-gray-300">{{ sub.max_teams }}</td>
                <td class="p-2">
                    <div class="flex items-center gap-2">
                        <select
                            :value="sub.subscription_plan_id"
                            @change="$emit('changePlan', sub, $event.target.value)"
                            class="rounded border border-gray-600 bg-gray-900 px-2 py-1 text-gray-200"
                        >
                            <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                {{ plan.name }}
                            </option>
                        </select>
                        <button
                            v-if="!['cancelled', 'expired'].includes(sub.status)"
                            @click="$emit('cancel', sub)"
                            class="rounded border border-amber-700 bg-amber-950 px-2 py-1 font-bold text-amber-300"
                        >
                            Disable
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    plans: {
        type: Array,
        default: () => []
    }
})

defineEmits(['approve', 'cancel', 'changePlan'])
</script>
