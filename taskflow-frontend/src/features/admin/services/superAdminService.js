// Servis za super admin panel: agregirani dashboard podaci (klubovi, korisnici, pretplate)
// i akcija odobravanja pretplate.
import api from '../../../services/api'

export function fetchSuperAdminDashboard() {
    return api.get('/super-admin/dashboard')
}

export function approveSubscriptionRequest(subscriptionId) {
    return api.patch(`/subscriptions/${subscriptionId}/status`, { status: 'active' })
}

export function deleteUserRequest(userId) {
    return api.delete(`/super-admin/users/${userId}`)
}

export function deleteClubRequest(clubId) {
    return api.delete(`/super-admin/clubs/${clubId}`)
}

export function cancelSubscriptionRequest(subscriptionId) {
    return api.patch(`/super-admin/subscriptions/${subscriptionId}/cancel`)
}

export function changeSubscriptionPlanRequest(subscriptionId, planId) {
    return api.patch(`/super-admin/subscriptions/${subscriptionId}/plan`, { plan_id: planId })
}
