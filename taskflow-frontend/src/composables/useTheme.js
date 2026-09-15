import { ref } from 'vue'

const getInitialTheme = () => {
  if (typeof window === 'undefined') return true
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    return savedTheme === 'dark'
  }
  return document.documentElement.classList.contains('dark') ||
    window.matchMedia('(prefers-color-scheme: dark)').matches
}

const isDark = ref(getInitialTheme())

const applyTheme = (dark) => {
  if (typeof document === 'undefined') return
  const htmlEl = document.documentElement
  if (dark) {
    htmlEl.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    htmlEl.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

// Inicijalna primena
if (typeof window !== 'undefined') {
  applyTheme(isDark.value)

  // Osluškivanje sistemske teme ukoliko korisnik nema eksplicitno sačuvano
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      isDark.value = e.matches
      applyTheme(e.matches)
    }
  })
}

export function useTheme() {
  const toggleTheme = () => {
    isDark.value = !isDark.value
    applyTheme(isDark.value)
  }

  const setTheme = (theme) => {
    isDark.value = theme === 'dark'
    applyTheme(isDark.value)
  }

  return {
    isDark,
    toggleTheme,
    setTheme
  }
}