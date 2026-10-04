export const DEFAULT_FIELD_SPOTS = [
    { id: 1, position: 'GK', roleName: 'Golman', defaultNumber: 1, x: 8, y: 50, player: null },
    { id: 2, position: 'RB', roleName: 'Desni Bek', defaultNumber: 2, x: 25, y: 18, player: null },
    { id: 3, position: 'CB', roleName: 'Štoper D', defaultNumber: 5, x: 25, y: 40, player: null },
    { id: 4, position: 'CB', roleName: 'Štoper L', defaultNumber: 4, x: 25, y: 60, player: null },
    { id: 5, position: 'LB', roleName: 'Levi Bek', defaultNumber: 3, x: 25, y: 82, player: null },
    { id: 6, position: 'CM', roleName: 'Vezni D', defaultNumber: 10, x: 50, y: 30, player: null },
    { id: 7, position: 'CM', roleName: 'Vezni C', defaultNumber: 8, x: 48, y: 50, player: null },
    { id: 8, position: 'CM', roleName: 'Vezni L', defaultNumber: 6, x: 50, y: 70, player: null },
    { id: 9, position: 'RW', roleName: 'Desno Krilo', defaultNumber: 7, x: 78, y: 20, player: null },
    { id: 10, position: 'ST', roleName: 'Napadač', defaultNumber: 9, x: 85, y: 50, player: null },
    { id: 11, position: 'LW', roleName: 'Levo Krilo', defaultNumber: 11, x: 78, y: 80, player: null },
]

export function createDefaultFieldSpots() {
    return DEFAULT_FIELD_SPOTS.map(spot => ({ ...spot }))
}

export const FORMATION_LAYOUTS = {
    '4-3-3': [
        { x: 25, y: 18 },
        { x: 25, y: 40 },
        { x: 25, y: 60 },
        { x: 25, y: 82 },
        { x: 50, y: 30 },
        { x: 48, y: 50 },
        { x: 50, y: 70 },
        { x: 78, y: 20 },
        { x: 85, y: 50 },
        { x: 78, y: 80 },
    ],
    '4-4-2': [
        { x: 25, y: 15 },
        { x: 25, y: 38 },
        { x: 25, y: 62 },
        { x: 25, y: 85 },
        { x: 50, y: 15 },
        { x: 50, y: 38 },
        { x: 50, y: 62 },
        { x: 50, y: 85 },
        { x: 82, y: 38 },
        { x: 82, y: 62 },
    ],
    '4-2-3-1': [
        { x: 25, y: 15 },
        { x: 25, y: 38 },
        { x: 25, y: 62 },
        { x: 25, y: 85 },
        { x: 45, y: 38 },
        { x: 45, y: 62 },
        { x: 68, y: 20 },
        { x: 68, y: 50 },
        { x: 68, y: 80 },
        { x: 86, y: 50 },
    ],
    '3-5-2': [
        { x: 25, y: 30 },
        { x: 25, y: 50 },
        { x: 25, y: 70 },
        { x: 50, y: 10 },
        { x: 50, y: 30 },
        { x: 50, y: 50 },
        { x: 50, y: 70 },
        { x: 50, y: 90 },
        { x: 82, y: 40 },
        { x: 82, y: 60 },
    ]
}
