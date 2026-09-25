import { ref, reactive } from 'vue'
import api from '../../../services/api'

export function useRegisterForm() {
    const loading = ref(false)
    const successMessage = ref('')

    const form = reactive({
        name: '',
        club_name: '',
        city: '',
        phone: '',
        email: '',
        password: ''
        ,password_confirmation: ''
    })

    const handleRegister = async () => {
        loading.value = true
        successMessage.value = ''
        try {
            const response = await api.post('/register', form)
            successMessage.value = `Registracija uspešna. Proverite email za onboarding link.${
                response.data?.data?.onboarding_url ? ` Link: ${response.data.data.onboarding_url}` : ''
            }`
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri registraciji.')
        } finally {
            loading.value = false
        }
    }

    return {
        form,
        loading,
        successMessage,
        handleRegister
    }
}
