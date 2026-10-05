const AGE_GROUP_LIMITS = {
    u9: 9,
    u11: 11,
    u13: 13,
    u15: 15,
    u17: 17,
    u19: 19,
    senior: Number.POSITIVE_INFINITY
}

export function getPlayerAge(player) {
    if (!player?.date_of_birth) return null

    const [year, month, day] = String(player.date_of_birth).slice(0, 10).split('-').map(Number)
    if (!year || !month || !day) return null

    const today = new Date()
    let age = today.getFullYear() - year
    if (today.getMonth() + 1 < month || (today.getMonth() + 1 === month && today.getDate() < day)) {
        age -= 1
    }

    return age >= 0 ? age : null
}

export function formatPlayerAge(player) {
    const age = getPlayerAge(player)
    if (age === null) return 'Uzrast nije unet'

    const lastTwoDigits = age % 100
    const lastDigit = age % 10
    const unit = lastTwoDigits >= 11 && lastTwoDigits <= 14
        ? 'godina'
        : lastDigit === 1
            ? 'godina'
            : lastDigit >= 2 && lastDigit <= 4
                ? 'godine'
                : 'godina'

    return `${age} ${unit}`
}

export function getAgeGroupLimit(ageGroup) {
    return AGE_GROUP_LIMITS[String(ageGroup || '').toLowerCase()] ?? null
}

export function getAgeGroupForAge(age) {
    if (age <= 9) return 'u9'
    if (age <= 11) return 'u11'
    if (age <= 13) return 'u13'
    if (age <= 15) return 'u15'
    if (age <= 17) return 'u17'
    if (age <= 19) return 'u19'
    return 'senior'
}