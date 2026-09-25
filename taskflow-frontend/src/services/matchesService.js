// Servis za rad sa utakmicama preko backend API-ja.
import api from './api'

export function fetchMatches() {
    return api.get('/matches')
}

export function createMatch(payload) {
    return api.post('/matches', payload)
}

export function deleteMatch(matchId) {
    return api.delete(`/matches/${matchId}`)
}
