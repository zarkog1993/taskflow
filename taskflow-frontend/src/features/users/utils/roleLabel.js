const ROLE_LABELS = new Map([
    ['admin', 'Administrator'],
    ['super admin', 'Super administrator'],
    ['super-admin', 'Super administrator'],
    ['club admin', 'Administrator kluba'],
    ['club-admin', 'Administrator kluba'],
    ['player', 'Igrač'],
    ['coach', 'Trener']
])

export function getRoleLabel(role) {
    const label = typeof role === 'string' ? role.trim() : ''
    if (!label) return 'Bez uloge'

    return ROLE_LABELS.get(label.toLowerCase()) || label
}