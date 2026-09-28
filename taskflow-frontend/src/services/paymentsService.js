// Servis za finansije: članarine (akademija) i isplate/honorari (seniori).
import api from './api'
import { extractItem } from './apiResponse'

/**
 * Objedinjeni finansijski pregled za ceo klub ili jednu ekipu.
 * @param {{ teamId?: number|string|null, period: string, type: 'membership'|'stipend' }} params
 * @returns {Promise<{ period: string, type: string, summary: object, rows: object[] }>}
 */
export function fetchFinancesOverview({ teamId = null, period, type }) {
  return api
    .get('/finances/overview', {
      params: {
        team_id: teamId || undefined,
        period,
        type
      }
    })
    .then(extractItem)
}

/**
 * Snima ili ažurira uplatu/isplatu za igrača.
 * Upsert ključ na backendu: team_id + player_id + type + period.
 * @param {{ team_id: number, player_id: number, type: string, period: string, amount: number, status: string, note?: string }} payload
 */
export function savePayment(payload) {
  return api.post('/payments', payload).then(extractItem)
}

/**
 * Masovni upsert — primenjuje isti mesečni iznos/status na više igrača odjednom.
 * @param {{ type: string, period: string, items: Array<{ team_id: number, player_id: number, amount: number, status: string }> }} payload
 */
export function bulkSavePayments(payload) {
  return api.post('/payments/bulk', payload).then(extractItem)
}

/**
 * Dohvata evidentirane uplate/isplate za određeni tim i period (YYYY-MM).
 * Vraća sirov axios odgovor zbog postojećih poziva.
 */
export function getTeamPayments(teamId, period, type = null) {
  return api.get(`/teams/${teamId}/payments`, {
    params: { period, type: type || undefined }
  })
}

export default {
  fetchFinancesOverview,
  savePayment,
  bulkSavePayments,
  getTeamPayments
}
