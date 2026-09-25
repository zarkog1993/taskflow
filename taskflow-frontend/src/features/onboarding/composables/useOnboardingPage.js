import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../../services/api'

export function useOnboardingPage() {
    const route = useRoute()
    const router = useRouter()

    const club = ref(null)
    const user = ref(null)
    const plans = ref([])
    const selectedPlan = ref('')
    const loading = ref(true)
    const submitting = ref(false)
    const message = ref('')
    const error = ref('')

    // Dohvaćamo token bez obzira je li poslan kao query (?token=xyz) ili kao ruta (/onboarding/xyz)
    const getToken = () => {
        return route.query.token || route.params.token || ''
    }

    onMounted(async () => {
        const token = getToken()

        if (!token) {
            error.value = 'Nedostaje onboarding token u URL-u.'
            loading.value = false
            return
        }

        try {
            const response = await api.get(`/onboarding/${encodeURIComponent(token)}`)

            user.value = response.data.user || null
            club.value = response.data.club || response.data.user?.club || null

            // Podrška za objekte ili polja u response.data.plans
            const rawPlans = response.data.plans || []
            if (Array.isArray(rawPlans)) {
                plans.value = rawPlans
            } else {
                plans.value = Object.keys(rawPlans).map(key => ({
                    slug: key,
                    ...rawPlans[key]
                }))
            }

            selectedPlan.value = plans.value[0]?.slug || plans.value[0]?.type || 'basic'
        } catch (err) {
            error.value = err.response?.data?.message || 'Onboarding link je nevažeći ili je istekao.'
        } finally {
            loading.value = false
        }
    })

    const submit = async () => {
        submitting.value = true
        error.value = ''
        const token = getToken()

        try {
            await api.post(`/onboarding/${encodeURIComponent(token)}/select`, {
                plan_type: selectedPlan.value
            })

            message.value = 'Paket je uspješno odabran! Vaša pretplata čeka odobrenje administratora.'
            setTimeout(() => router.push({ name: 'subscription-pending' }), 1800)
        } catch (err) {
            error.value = err.response?.data?.message || 'Nije moguće odabrati ovaj paket.'
        } finally {
            submitting.value = false
        }
    }

    return {
        club,
        user,
        plans,
        selectedPlan,
        loading,
        submitting,
        message,
        error,
        submit
    }
}