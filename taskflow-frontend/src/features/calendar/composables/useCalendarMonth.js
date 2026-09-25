// Composable koji gradi mesečnu mrežu kalendara: navigaciju kroz mesece i raspored
// dana (uključujući dane susednih meseci za popunu grida) sa pripadajućim događajima.
import { ref, computed } from 'vue'

export function useCalendarMonth(events) {
    const currentDate = ref(new Date())

    const currentYear = computed(() => currentDate.value.getFullYear())
    const currentMonthName = computed(() =>
        currentDate.value.toLocaleString('sr-RS', { month: 'long' })
    )

    const prevMonth = () => {
        currentDate.value = new Date(
            currentDate.value.getFullYear(),
            currentDate.value.getMonth() - 1,
            1
        )
    }

    const nextMonth = () => {
        currentDate.value = new Date(
            currentDate.value.getFullYear(),
            currentDate.value.getMonth() + 1,
            1
        )
    }

    const calendarDays = computed(() => {
        const year = currentDate.value.getFullYear()
        const month = currentDate.value.getMonth()

        const firstDayOfMonth = new Date(year, month, 1)
        const lastDayOfMonth = new Date(year, month + 1, 0)

        let startingDayOfWeek = firstDayOfMonth.getDay() - 1
        if (startingDayOfWeek === -1) startingDayOfWeek = 6

        const days = []
        const today = new Date()

        for (let i = startingDayOfWeek; i > 0; i--) {
            const d = new Date(year, month, 1 - i)
            days.push({
                date: d,
                isCurrentMonth: false,
                isToday: false,
                events: []
            })
        }

        for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
            const d = new Date(year, month, i)
            const dayEvents = events.value.filter((e) => {
                return (
                    e.scheduled_at.getDate() === d.getDate() &&
                    e.scheduled_at.getMonth() === d.getMonth() &&
                    e.scheduled_at.getFullYear() === d.getFullYear()
                )
            })

            const isToday =
                d.getDate() === today.getDate() &&
                d.getMonth() === today.getMonth() &&
                d.getFullYear() === today.getFullYear()

            days.push({
                date: d,
                isCurrentMonth: true,
                isToday,
                events: dayEvents
            })
        }

        return days
    })

    return {
        currentDate,
        currentYear,
        currentMonthName,
        prevMonth,
        nextMonth,
        calendarDays
    }
}
