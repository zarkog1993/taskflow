// Servis za rad sa timovima (ekipama) preko backend API-ja.
import api from './api'
import { extractList, extractItem } from './apiResponse'

export function fetchTeams() {
    return api.get('/teams').then(extractList)
}

export function fetchTeam(teamId) {
    return api.get(`/teams/${teamId}`).then(extractItem)
}

export function createTeam(payload) {
    return api.post('/teams', payload)
}
