<template>
    <div v-if="player" class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka: Nazad i Izmeni -->
        <div class="flex justify-between items-center bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <router-link to="/players" class="px-4 py-2 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl transition flex items-center gap-2">
                ← Nazad na Registar
            </router-link>

            <button @click="openEditModal" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition cursor-pointer flex items-center gap-2">
                ✏️ Izmeni Podatke Igrača
            </button>
        </div>

        <!-- Statistički Sažetak na Vrhu -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-indigo-400 font-mono">{{ getStat('trainings_attended') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Treninga</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-emerald-400 font-mono">{{ getStat('matches_played') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Utakmica</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-amber-400 font-mono">{{ getStat('goals') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Golova</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-sky-400 font-mono">{{ getStat('assists') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Asistencija</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEVA KOLONA: Osnovne Kartice Igrača -->
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-full bg-indigo-950 border-4 border-indigo-600/50 shadow-2xl overflow-hidden relative flex items-center justify-center font-black text-3xl text-indigo-300">
                        <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                        <span v-else>#{{ player.jersey_number || '-' }}</span>

                        <span class="absolute bottom-1 right-1 bg-emerald-500 text-gray-950 text-[10px] font-black px-2 py-0.5 rounded-full border-2 border-gray-900 shadow">
              #{{ player.jersey_number || '?' }}
            </span>
                    </div>

                    <h2 class="text-2xl font-black text-white mt-4">{{ player.name }}</h2>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ player.email || 'Nema upisan email' }}</p>

                    <div class="flex items-center gap-2 mt-3">
            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-950 text-indigo-400 border border-indigo-800 uppercase">
              {{ player.primary_position }}
            </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-900 text-gray-300 border border-gray-700 uppercase">
              {{ player.seniority || 'Seniori' }}
            </span>
                    </div>
                </div>

                <div class="border-t border-gray-700/60 pt-4 space-y-3 text-xs">
                    <h3 class="font-bold uppercase text-gray-400 tracking-wider text-[10px]">Detaljni Podaci Profila</h3>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Visina:</span>
                            <span class="text-white font-mono font-bold">{{ player.height ? player.height + ' cm' : 'Nije uneto' }}</span>
                        </div>
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Težina:</span>
                            <span class="text-white font-mono font-bold">{{ player.weight ? player.weight + ' kg' : 'Nije uneto' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Jača noga:</span>
                            <span class="text-white font-bold capitalize">{{ formatFoot(player.preferred_foot) }}</span>
                        </div>
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Datum rođenja:</span>
                            <span class="text-white font-mono font-bold">{{ player.date_of_birth || 'Nije uneto' }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-900/80 p-3.5 rounded-xl border border-gray-700/60 space-y-1">
                        <span class="text-indigo-400 text-[10px] block font-bold uppercase">📝 Beleška Trenera:</span>
                        <p class="text-gray-300 italic leading-relaxed">{{ player.coach_notes || 'Nema zabeleženih opaski trenera.' }}</p>
                    </div>
                </div>
            </div>

            <!-- DESNA KOLONA: Mapa Pozicije na Terenu -->
            <div class="lg:col-span-2 bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold uppercase text-gray-400 tracking-wider">Pozicija na Terenu</h3>
                        <span class="text-xs font-black text-emerald-400 bg-emerald-950 px-2.5 py-1 rounded border border-emerald-800">
              {{ player.primary_position }}
            </span>
                    </div>

                    <div class="relative w-full aspect-[1.5/1] bg-emerald-950/40 rounded-2xl border-2 border-emerald-800/60 overflow-hidden select-none">
                        <svg class="absolute inset-0 w-full h-full stroke-emerald-500/30 fill-none pointer-events-none" stroke-width="2">
                            <rect x="2" y="2" width="99%" height="96%" rx="10" />
                            <line x1="50%" y1="0" x2="50%" y2="100%" />
                            <circle cx="50%" cy="50%" r="12%" />
                            <rect x="2" y="22%" width="16%" height="56%" />
                            <rect x="82%" y="22%" width="16%" height="56%" />
                        </svg>

                        <div
                            class="absolute -translate-x-1/2 -translate-y-1/2 flex flex-col items-center transition-all duration-300"
                            :style="getPositionStyle(player.primary_position)"
                        >
                            <div class="w-12 h-12 bg-emerald-500 text-gray-950 font-black rounded-full flex items-center justify-center border-2 border-white shadow-2xl animate-pulse">
                                {{ player.primary_position }}
                            </div>
                            <span class="text-[10px] font-bold text-white bg-gray-950/90 px-2 py-0.5 rounded mt-1 border border-gray-800 shadow">
                Primarna Pozicija
              </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL FORMA ZA IZMENU PODATAKA IGRAČA -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="p-5 bg-gray-900 border-b border-gray-700/70 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-white">Izmeni Podatke Igrača</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ editForm.name }}</p>
                    </div>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="handleUpdatePlayer" class="p-6 space-y-4 overflow-y-auto">
                    <!-- Nova Slika -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Promeni Sliku (Max 500 KB)</label>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-900 border border-gray-700 overflow-hidden flex items-center justify-center shrink-0">
                                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                                <img v-else-if="player.photo_url" :src="player.photo_url" class="w-full h-full object-cover" />
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
                            <input v-model="editForm.name" type="text" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Email</label>
                            <input v-model="editForm.email" type="email" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500" />
                        </div>
                    </div>

                    <!-- Pozicija i Senioritet -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Pozicija *</label>
                            <select v-model="editForm.primary_position" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
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
                            <select v-model="editForm.seniority" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
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
                            <input v-model.number="editForm.height" type="number" placeholder="185" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Težina (kg)</label>
                            <input v-model.number="editForm.weight" type="number" placeholder="78" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Datum Rođenja</label>
                            <input v-model="editForm.date_of_birth" type="date" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2.5 text-xs text-white outline-none font-mono" />
                        </div>
                    </div>

                    <!-- Jača Noga i Broj Dresa -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Jača Noga</label>
                            <select v-model="editForm.preferred_foot" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none cursor-pointer">
                                <option value="right">Desna</option>
                                <option value="left">Leva</option>
                                <option value="both">Obe</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Broj Dresa</label>
                            <input v-model.number="editForm.jersey_number" type="number" placeholder="10" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center font-mono" />
                        </div>
                    </div>

                    <!-- Beleška Trenera -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Beleška Trenera</label>
                        <textarea v-model="editForm.coach_notes" rows="2" placeholder="Taktičke opaske..." class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Akcije -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700/70 shrink-0">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white transition cursor-pointer">Odustani</button>
                        <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition border border-indigo-500/30 cursor-pointer disabled:opacity-50">
                            <span v-if="isSubmitting" class="animate-spin">⏳</span>
                            <span>Sačuvaj Promene</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div v-else class="text-center py-20 text-gray-400">
        <div class="animate-spin text-2xl mb-2">⏳</div>
        <p class="text-xs">Učitavanje profila igrača...</p>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const player = ref(null)
const showEditModal = ref(false)
const isSubmitting = ref(false)

const selectedPhoto = ref(null)
const photoPreview = ref(null)
const photoError = ref("")

const editForm = reactive({
    name: "",
    email: "",
    primary_position: "CM",
    seniority: "Seniori",
    height: null,
    weight: null,
    date_of_birth: "",
    preferred_foot: "right",
    jersey_number: null,
    coach_notes: "",
})

const fetchPlayerProfile = async () => {
    try {
        const res = await api.get(`/players/${route.params.id}`)
        player.value = res.data.data || res.data
    } catch (err) {
        console.error('Greška pri učitavanju profila igrača:', err)
    }
}

const openEditModal = () => {
    if (!player.value) return
    editForm.name = player.value.name || ""
    editForm.email = player.value.email || ""
    editForm.primary_position = player.value.primary_position || "CM"
    editForm.seniority = player.value.seniority || "Seniori"
    editForm.height = player.value.height || null
    editForm.weight = player.value.weight || null
    editForm.date_of_birth = player.value.date_of_birth || ""
    editForm.preferred_foot = player.value.preferred_foot || "right"
    editForm.jersey_number = player.value.jersey_number || null
    editForm.coach_notes = player.value.coach_notes || ""
    selectedPhoto.value = null
    photoPreview.value = null
    photoError.value = ""
    showEditModal.value = true
}

const handlePhotoSelect = (event) => {
    const file = event.target.files[0]
    photoError.value = ""

    if (!file) {
        selectedPhoto.value = null
        photoPreview.value = null
        return
    }

    if (file.size > 500 * 1024) {
        photoError.value = "Slika je prevelika! Maksimalna dozvoljena veličina je 500 KB."
        event.target.value = ""
        selectedPhoto.value = null
        photoPreview.value = null
        return
    }

    selectedPhoto.value = file
    photoPreview.value = URL.createObjectURL(file)
}

const handleUpdatePlayer = async () => {
    if (photoError.value) return
    isSubmitting.value = true

    try {
        const formData = new FormData()
        // Laravel Multipart POST sa simularnim PUT pretvaranjem (_method)
        formData.append('_method', 'PUT')
        formData.append('name', editForm.name)
        formData.append('primary_position', editForm.primary_position)
        formData.append('seniority', editForm.seniority)

        if (editForm.email) formData.append('email', editForm.email)
        if (editForm.jersey_number) formData.append('jersey_number', editForm.jersey_number)
        if (editForm.height) formData.append('height', editForm.height)
        if (editForm.weight) formData.append('weight', editForm.weight)
        if (editForm.date_of_birth) formData.append('date_of_birth', editForm.date_of_birth)
        if (editForm.preferred_foot) formData.append('preferred_foot', editForm.preferred_foot)
        if (editForm.coach_notes) formData.append('coach_notes', editForm.coach_notes)
        if (selectedPhoto.value) formData.append('photo', selectedPhoto.value)

        await api.post(`/players/${player.value.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        showEditModal.value = false
        await fetchPlayerProfile()
    } catch (err) {
        console.error('Greška pri ažuriranju igrača:', err)
        alert(err.response?.data?.message || 'Došlo je do greške prilikom ažuriranja igrača.')
    } finally {
        isSubmitting.value = false
    }
}

const getStat = (field) => {
    if (!player.value) return 0
    if (player.value.stats && player.value.stats[field] !== undefined) {
        return player.value.stats[field]
    }
    return player.value[field] || 0
}

const formatFoot = (foot) => {
    if (foot === 'right') return 'Desna'
    if (foot === 'left') return 'Leva'
    if (foot === 'both') return 'Obe'
    return 'Nije podešeno'
}

const getPositionStyle = (pos) => {
    const positions = {
        GK: { left: '8%', top: '50%' },
        LB: { left: '25%', top: '82%' },
        CB: { left: '25%', top: '50%' },
        RB: { left: '25%', top: '18%' },
        DM: { left: '40%', top: '50%' },
        CM: { left: '50%', top: '50%' },
        AM: { left: '65%', top: '50%' },
        LW: { left: '78%', top: '80%' },
        RW: { left: '78%', top: '20%' },
        ST: { left: '85%', top: '50%' },
    }
    return positions[pos] || { left: '50%', top: '50%' }
}

onMounted(fetchPlayerProfile)
</script>