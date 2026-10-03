import { ref } from 'vue'

const getInitialTheme = () => {
	if (typeof window === 'undefined') return true

	const savedTheme = localStorage.getItem('theme')

	if (savedTheme === 'dark' || savedTheme === 'light') {
		return savedTheme === 'dark'
	}

	return window.matchMedia('(prefers-color-scheme: dark)').matches
}

const isDark = ref(getInitialTheme())

const applyTheme = (dark) => {
	if (typeof document === 'undefined') return

	document.documentElement.classList.toggle('dark', dark)
}

// Inicijalna primena
if (typeof window !== 'undefined') {
	applyTheme(isDark.value)

	// Osluškivanje sistemske teme ukoliko korisnik nema eksplicitno sačuvano
	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
		const savedTheme = localStorage.getItem('theme')
		if (savedTheme !== 'dark' && savedTheme !== 'light') {
			isDark.value = e.matches
			applyTheme(e.matches)
		}
	})
}

export function useTheme() {
	const setTheme = (theme) => {
		isDark.value = theme === 'dark'
		localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
		applyTheme(isDark.value)
	}

	const toggleTheme = () => setTheme(isDark.value ? 'light' : 'dark')

	return {
		isDark,
		toggleTheme,
		setTheme
	}
}