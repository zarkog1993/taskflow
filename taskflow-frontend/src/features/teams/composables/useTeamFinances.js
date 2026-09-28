import { ref, watch } from 'vue'
import paymentsService from '../../../services/paymentsService'

export function useTeamFinances(teamId) {
    const showModal = ref(false)
    const activeType = ref('membership') // 'membership' ili 'stipend'
    const selectedPeriod = ref(new Date().toISOString().slice(0, 7)) // YYYY-MM
    const loading = ref(false)
    const paymentsMap = ref({})

    // Učitavanje uplatne liste s bekenda
    const loadPayments = async () => {
        if (!teamId.value) return
        loading.value = true
        try {
            const response = await paymentsService.getTeamPayments(teamId.value, selectedPeriod.value)
            const map = {}
            if (response.data && response.data.payments) {
                response.data.payments.forEach(p => {
                    map[p.player_id] = p
                })
            }
            paymentsMap.value = map
        } catch (err) {
            console.error('Greška pri učitavanju finansija:', err)
        } finally {
            loading.value = false
        }
    }

    // Dohvata iznos/status za pojedinog igrača ili postavlja podrazumijevane vrijednosti
    const getPayment = (playerId) => {
        if (!paymentsMap.value[playerId]) {
            paymentsMap.value[playerId] = {
                player_id: playerId,
                team_id: teamId.value,
                amount: activeType.value === 'membership' ? 3000 : 15000,
                status: 'pending'
            }
        }
        return paymentsMap.value[playerId]
    }

    // Snima promjene na backend
    const savePayment = async (playerId) => {
        const pay = getPayment(playerId)
        try {
            await paymentsService.savePayment({
                team_id: teamId.value,
                player_id: playerId,
                type: activeType.value,
                period: selectedPeriod.value,
                amount: pay.amount,
                status: pay.status
            })
        } catch (err) {
            console.error('Greška pri čuvanju uplate:', err)
        }
    }

    // Brza promjena statusa
    const updateStatus = (playerId, newStatus) => {
        const pay = getPayment(playerId)
        pay.status = newStatus
        savePayment(playerId)
    }

    const openFinancesModal = () => {
        showModal.value = true
        loadPayments()
    }

    const closeFinancesModal = () => {
        showModal.value = false
    }

    // Prati promjenu taba (Članarina / Isplata) ili mjeseca
    watch([activeType, selectedPeriod], () => {
        if (showModal.value) loadPayments()
    })

    return {
        showModal,
        activeType,
        selectedPeriod,
        loading,
        getPayment,
        updateStatus,
        savePayment,
        openFinancesModal,
        closeFinancesModal
    }
}