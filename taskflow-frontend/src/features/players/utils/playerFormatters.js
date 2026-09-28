// Kategorija igrača se izvodi iz starosne grupe ekipe (teams.age_group).
const AGE_GROUP_LABELS = {
    senior: 'Seniori',
    u19: 'U19',
    u17: 'U17',
    u15: 'U15',
    u13: 'U13',
    u11: 'U11',
    u9: 'U9'
}

export function formatAgeGroup(ageGroup) {
    if (!ageGroup) return ''
    return AGE_GROUP_LABELS[String(ageGroup).toLowerCase()] || String(ageGroup).toUpperCase()
}

export function getPlayerCategory(player) {
    return formatAgeGroup(player?.team?.age_group) || player?.seniority || 'Nesvrstan'
}
