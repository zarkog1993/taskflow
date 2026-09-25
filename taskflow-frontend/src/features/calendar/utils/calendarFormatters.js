// Pomoćne funkcije za normalizaciju događaja (treninzi/utakmice) i formatiranje datuma.

export function normalizeTraining(t) {
    return {
        ...t,
        type: 'training',
        title: t.title,
        location: t.location,
        team: t.team,
        users: t.users || t.attendees || [],
        scheduled_at: new Date(t.scheduled_at),
        time: new Date(t.scheduled_at).toLocaleTimeString('sr-RS', {
            hour: '2-digit',
            minute: '2-digit'
        })
    }
}

export function normalizeMatch(m) {
    return {
        ...m,
        type: 'match',
        title: `vs ${m.opponent}`,
        location: m.location,
        team: m.team,
        users: m.users || m.players || [],
        scheduled_at: new Date(m.scheduled_at),
        time: new Date(m.scheduled_at).toLocaleTimeString('sr-RS', {
            hour: '2-digit',
            minute: '2-digit'
        })
    }
}

export function formatDate(dateObj) {
    if (!dateObj) return ''
    const d = new Date(dateObj)
    return d.toLocaleString('sr-RS', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
