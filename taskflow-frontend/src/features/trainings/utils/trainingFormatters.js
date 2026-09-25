// Zajedničke pomoćne funkcije za formatiranje i izračunavanje prisustva na treninzima.

export function getDayName(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('sr-RS', { weekday: 'short' })
}

export function getDayNumber(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).getDate()
}

export function formatTime(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleTimeString('sr-RS', { hour: '2-digit', minute: '2-digit' })
}

export function getAttendedCount(session) {
    const list = session.users || session.attendees || []
    return list.filter(u => u.pivot?.attended === 1 || u.pivot?.attended === true).length
}
