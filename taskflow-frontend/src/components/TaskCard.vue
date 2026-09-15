<template>
  <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-xl p-4 shadow-sm hover:border-indigo-500/50 transition">
    <div class="flex justify-between items-start mb-2">
      <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100 leading-snug">{{ task.title }}</h4>

      <!-- Bedž za prioritet -->
      <span 
        :class="[
          'text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full',
          task.priority === 'high' ? 'bg-red-500/15 text-red-600 dark:bg-red-500/20 dark:text-red-400 border border-red-500/30' :
          task.priority === 'medium' ? 'bg-yellow-500/15 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400 border border-yellow-500/30' :
          'bg-blue-500/15 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 border border-blue-500/30'
        ]"
      >
        {{ task.priority || 'low' }}
      </span>
    </div>

    <p v-if="task.description" class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
      {{ task.description }}
    </p>

    <!-- Prebacivanje statusa (To Do -> In Progress -> Done) -->
    <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700/50 text-xs">
      <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
        {{ task.assigned_user?.name || task.user?.name || 'Nedodeljeno' }}
      </span>

      <select 
        :value="task.status" 
        @change="$emit('change-status', task.id, $event.target.value)"
        class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-[11px] rounded-lg px-2 py-1 outline-none focus:border-indigo-500"
      >
        <option value="todo">To Do</option>
        <option value="in_progress">In Progress</option>
        <option value="done">Done</option>
      </select>
    </div>
  </div>
</template>

<script setup>
defineProps({
  task: {
    type: Object,
    required: true
  }
})

defineEmits(['change-status'])
</script>