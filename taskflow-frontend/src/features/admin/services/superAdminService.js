// Servis za super admin panel: agregirani dashboard podaci (klubovi, korisnici, pretplate)
// i akcija odobravanja pretplate.
import api from '../../../services/api'

export function fetchSuperAdminDashboard() {
    return api.get('/super-admin/dashboard')
}

export function approveSubscriptionRequest(subscriptionId) {
    return api.patch(`/subscriptions/${subscriptionId}/status`, { status: 'active' })
}
