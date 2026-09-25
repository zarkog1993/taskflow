// Zajedničke pomoćne funkcije za normalizaciju odgovora backend API-ja.
// Laravel resursi vraćaju liste/objekte umotane u `{ data: ... }`, dok pojedini
// endpointi mogu vratiti sirove podatke bez omota — ove funkcije to ujednačavaju.

export function extractList(response) {
    return response.data?.data || response.data || []
}

export function extractItem(response) {
    return response.data?.data || response.data
}
