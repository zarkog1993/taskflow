<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-5 bg-gray-900 border-b border-gray-700/70 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-black text-white">Izmeni Podatke Igrača</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ form.name }}</p>
                </div>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">✕</button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6 space-y-4 overflow-y-auto">
                <!-- Nova Slika -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Promeni Sliku (Max 500 KB)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-900 border border-gray-700 overflow-hidden flex items-center justify-center shrink-0">
                            <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                            <img v-else-if="currentPhotoUrl" :src="currentPhotoUrl" class="w-full h-full object-cover" />
                            <span v-else class="text-gray-500 text-xs">📷</span>
                        </div>
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            @change="handlePhotoSelect"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-300 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-950 file:text-indigo-400 hover:file:bg-indigo-900 cursor-pointer"
                        />
                    </div>
                    <p v-if="photoError" class="text-[10px] font-bold text-rose-400 mt-1">{{ photoError }}</p>
                </div>

                <!-- Ime i Prezime & Email -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Ime i Prezime *</label>
                        <input v-model="form.name" type="text" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500" />
                    </div>
                </div>

                <!-- Pozicija i Senioritet -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Pozicija *</label>
                        <select v-model="form.primary_position" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                            <option value="GK">GK - Golman</option>
                            <option value="CB">CB - Štoper</option>
                            <option value="LB">LB - Levi Bek</option>
                            <option value="RB">RB - Desni Bek</option>
                            <option value="CM">CM - Centralni Vezni</option>
                            <option value="DM">DM - Zadnji Vezni</option>
                            <option value="AM">AM - Prednji Vezni</option>
                            <option value="LW">LW - Levo Krilo</option>
                            <option value="RW">RW - Desno Krilo</option>
                            <option value="ST">ST - Napadač</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Senioritet</label>
                        <select v-model="form.seniority" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                            <option value="Seniori">Seniori</option>
                            <option value="U19">U19 (Omladinci)</option>
                            <option value="U17">U17 (Kadeti)</option>
                            <option value="U15">U15 (Pioniri)</option>
                        </select>
                    </div>
                </div>

                <!-- Visina, Težina i Datum Rođenja -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Visina (cm)</label>
                        <input v-model.number="form.height" type="number" placeholder="185" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Težina (kg)</label>
                        <input v-model.number="form.weight" type="number" placeholder="78" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Datum Rođenja</label>
                        <input v-model="form.date_of_birth" type="date" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2.5 text-xs text-white outline-none font-mono" />
                    </div>
                </div>

                <!-- Jača Noga i Broj Dresa -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Jača Noga</label>
                        <select v-model="form.preferred_foot" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none cursor-pointer">
                            <option value="right">Desna</option>
                            <option value="left">Leva</option>
                            <option value="both">Obe</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Broj Dresa</label>
                        <input v-model.number="form.jersey_number" type="number" placeholder="10" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                    </div>
                </div>

                <!-- Beleška Trenera -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Beleška Trenera</label>
                    <textarea v-model="form.coach_notes" rows="2" placeholder="Taktičke opaske..." class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500"></textarea>
                </div>

                <!-- Akcije -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700/70 shrink-0">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white transition cursor-pointer">Odustani</button>
                    <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition border border-indigo-500/30 cursor-pointer disabled:opacity-50">
                        <span v-if="isSubmitting" class="animate-spin">⏳</span>
                        <span>Sačuvaj Promene</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { usePhotoUpload } from '../../../composables/usePhotoUpload'

defineProps({
    form: {
        type: Object,
        required: true
    },
    currentPhotoUrl: {
        type: String,
        default: ''
    },
    isSubmitting: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close', 'submit'])

const { photoPreview, photoError, selectedPhoto, handlePhotoSelect } = usePhotoUpload()

const handleSubmit = () => {
    if (photoError.value) return
    emit('submit', selectedPhoto.value)
}
</script>
