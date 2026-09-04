<template>
    <div class="min-h-screen bg-gray-900 text-white">
        <!-- Navbar sa notifikacijama i korisničkim menijem -->

        <!-- Glavni sadržaj -->
        <main class="p-6 max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-200">Zadaci Projekta</h2>
                <button
                    @click="showModal = true"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        ></path>
                    </svg>
                    Novi Zadatak
                </button>
            </div>

            <!-- Kanban kolone idu ovde -->
            <div class="p-6 max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-200">Zadaci Projekta</h2>
                    <button
                        @click="showModal = true"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                        Novi Zadatak
                    </button>
                </div>

                <!-- Prikaz dok se učitava -->
                <div v-if="taskStore.loading" class="text-center py-12 text-gray-400">
                    Učitavanje zadataka...
                </div>

                <!-- Kanban Tabla -->
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kolona: To Do -->
                    <div
                        class="bg-gray-800/50 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]"
                    >
                        <div
                            class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700"
                        >
                            <h3
                                class="font-bold text-gray-300 uppercase text-xs tracking-wider flex items-center gap-2"
                            >
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                To Do
                            </h3>
                            <span
                                class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold"
                            >{{ taskStore.todoTasks.length }}</span
                            >
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                            <TaskCard
                                v-for="task in taskStore.todoTasks"
                                :key="task.id"
                                :task="task"
                                @change-status="taskStore.updateTaskStatus"
                            />
                        </div>
                    </div>

                    <!-- Kolona: In Progress -->
                    <div
                        class="bg-gray-800/50 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]"
                    >
                        <div
                            class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700"
                        >
                            <h3
                                class="font-bold text-yellow-400 uppercase text-xs tracking-wider flex items-center gap-2"
                            >
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                In Progress
                            </h3>
                            <span
                                class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold"
                            >{{ taskStore.inProgressTasks.length }}</span
                            >
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                            <TaskCard
                                v-for="task in taskStore.inProgressTasks"
                                :key="task.id"
                                :task="task"
                                @change-status="taskStore.updateTaskStatus"
                            />
                        </div>
                    </div>

                    <!-- Kolona: Done -->
                    <div
                        class="bg-gray-800/50 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]"
                    >
                        <div
                            class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700"
                        >
                            <h3
                                class="font-bold text-emerald-400 uppercase text-xs tracking-wider flex items-center gap-2"
                            >
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                Done
                            </h3>
                            <span
                                class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold"
                            >{{ taskStore.doneTasks.length }}</span
                            >
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                            <TaskCard
                                v-for="task in taskStore.doneTasks"
                                :key="task.id"
                                :task="task"
                                @change-status="taskStore.updateTaskStatus"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <!-- ... -->
            <!-- Modal za kreiranje zadatka -->
            <div
                v-if="showModal"
                class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50"
            >
                <div
                    class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl"
                >
                    <h3 class="text-xl font-bold text-white mb-4">
                        Kreiraj Novi Zadatak
                    </h3>

                    <form @submit.prevent="handleCreateTask" class="space-y-4">
                        <div>
                            <label
                                class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Naslov Zadatka</label
                            >
                            <input
                                v-model="newTask.title"
                                type="text"
                                required
                                placeholder="Unesite naslov..."
                                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Opis Zadatka</label
                            >
                            <textarea
                                v-model="newTask.description"
                                rows="3"
                                placeholder="Unesite detaljan opis..."
                                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Dodeljivanje korisnika -->
                        <div>
                            <label
                                class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Dodeli Korisniku</label
                            >
                            <select
                                v-model="newTask.assigned_user_id"
                                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                            >
                                <option :value="null">-- Bez dodeljenog korisnika --</option>
                                <option v-for="u in userStore.users" :key="u.id" :value="u.id">
                                    {{ u.name }} ({{ u.email }})
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                                >Status</label
                                >
                                <select
                                    v-model="newTask.status"
                                    class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none"
                                >
                                    <option value="todo">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                                >Prioritet</label
                                >
                                <select
                                    v-model="newTask.priority"
                                    class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="flex justify-end space-x-3 pt-4 border-t border-gray-700"
                        >
                            <button
                                type="button"
                                @click="showModal = false"
                                class="px-4 py-2 text-sm text-gray-400 hover:text-white"
                            >
                                Odustani
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded font-medium"
                            >
                                Kreiraj Zadatak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
// Ostali uvozi...
import { ref, reactive, onMounted } from "vue";
import { useTaskStore } from "../stores/task";
import { useUserStore } from "../stores/user";
import TaskCard from "../components/TaskCard.vue";

const taskStore = useTaskStore();
const userStore = useUserStore();

const showModal = ref(false);

const newTask = reactive({
    title: "",
    description: "",
    status: "todo",
    priority: "medium",
    assigned_user_id: null,
});

onMounted(() => {
    taskStore.fetchTasks();
    userStore.fetchUsers(); // Učitavamo korisnike za padajući meni
});

const handleCreateTask = async () => {
    const success = await taskStore.createTask(newTask);
    if (success) {
        showModal.value = false;
        newTask.title = "";
        newTask.description = "";
        newTask.status = "todo";
        newTask.priority = "medium";
        newTask.assigned_user_id = null;
    }
};
</script>