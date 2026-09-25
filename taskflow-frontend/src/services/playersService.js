// Servis za rad sa igračima preko backend API-ja.
// Kreiranje i potpuna izmena profila koriste multipart/form-data zbog upload-a slike,
// dok se parcijalne izmene (dodela ekipe, statistika) šalju kao običan JSON PUT.
import api from './api'
import { extractList, extractItem } from './apiResponse'

const multipartConfig = {
    headers: { 'Content-Type': 'multipart/form-data' }
}

export function fetchPlayers() {
    return api.get('/players').then(extractList)
}

export function fetchPlayer(playerId) {
    return api.get(`/players/${playerId}`).then(extractItem)
}

export function createPlayer(formData) {
    return api.post('/players', formData, multipartConfig)
}

// Laravel prima multipart PUT preko POST-a uz `_method=PUT` polje u formData-i.
export function updatePlayerMultipart(playerId, formData) {
    return api.post(`/players/${playerId}`, formData, multipartConfig)
}

export function updatePlayer(playerId, payload) {
    return api.put(`/players/${playerId}`, payload)
}

export function deletePlayer(playerId) {
    return api.delete(`/players/${playerId}`)
}

export function assignPlayerToTeam(playerId, teamId) {
    return updatePlayer(playerId, { team_id: teamId })
}

export function removePlayerFromTeam(playerId) {
    return updatePlayer(playerId, { team_id: null })
}
