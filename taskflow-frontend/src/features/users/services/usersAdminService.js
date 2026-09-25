// Servis za administraciju korisnika (nezavisno od "player registry" servisa igrača):
// lista korisnika/klubova, kreiranje i brisanje korisnika sa dodelom kluba i uloge.
import api from '../../../services/api'
import { extractList } from '../../../services/apiResponse'

export function fetchUsers() {
    return api.get('/users').then(extractList)
}

export function fetchClubs() {
    return api.get('/admin/clubs').then(extractList)
}

export function createUser(payload) {
    return api.post('/users', payload)
}

export function deleteUser(userId) {
    return api.delete(`/users/${userId}`)
}
