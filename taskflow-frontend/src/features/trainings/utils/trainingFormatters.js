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

// Laravel serijalizuje relaciju `invitedPlayers` kao `invited_players`.
export function getInvitedPlayers(session) {
    return session?.invited_players || session?.invitedPlayers || []
}

export function getRsvpStatus(player) {
    return player?.pivot?.status || 'pending'
}

export function getRsvpCounts(session) {
    const invited = getInvitedPlayers(session)
    const counts = { accepted: 0, declined: 0, pending: 0, total: invited.length }

    for (const player of invited) {
        counts[getRsvpStatus(player)] += 1
    }

    return counts
}

export function formatRespondedAt(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleString('sr-RS', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}
