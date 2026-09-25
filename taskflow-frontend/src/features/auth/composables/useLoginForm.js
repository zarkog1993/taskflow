// Composable koji drži stanje forme za prijavu i poziva auth store za login,
// zatim preusmerava na dashboard rutu nakon uspešne prijave.
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth'

export function useLoginForm() {
    const router = useRouter()
    const authStore = useAuthStore()

    const form = reactive({
        email: '',
        password: ''
    })

    const handleLogin = async () => {
        const success = await authStore.login(form)
        if (success) {
            router.push({ name: 'dashboard' })
        }
    }

    return {
        authStore,
        form,
        handleLogin
    }
}
