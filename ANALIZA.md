# TaskFlow — Analiza Projekta

> Generisano: 2026-09-29 · Grana: `main` · Poslednji commit: `c26b242`

---

## 1. Pregled

**TaskFlow** je SaaS aplikacija za upravljanje fudbalskim klubovima/akademijama — timovi, igrači, treninzi, utakmice, statistika i finansije. Monetizacija je kroz pretplatničke pakete sa feature-gating-om.

> **Napomena:** `README.md` je zastareo. Opisuje projekat kao "REST API za upravljanje zadacima" sa planiranim Tasks/Comments modulima. Projekat je u međuvremenu potpuno preusmeren na sportski domen. U bazi i dalje postoje prazne `tasks` i `comments` tabele kao ostatak.

| Metrika | Vrednost |
|---|---|
| Period razvoja | 2026-07-08 → 2026-09-29 (~3 meseca) |
| Broj commit-ova | 94 (65 + 29 od istog autora, dva git identiteta) |
| Backend LOC | ~3.953 (PHP) |
| Frontend LOC | ~9.405 (Vue/JS) |
| API endpoint-a | 44 + 2 `apiResource` (`users`, `players`) |
| Migracija | 42 |
| Testova | 32 (100 assertions) — **svi prolaze** |

---

## 2. Tehnološki stack

**Backend**
- PHP 8.3+ / Laravel 13.8
- Laravel Sanctum 4.0 (bearer token autentifikacija)
- MySQL 8.0, Redis 7

**Frontend**
- Vue 3.5 (Composition API, `<script setup>`)
- Vite 8.2, Pinia 4, Vue Router 4
- Tailwind CSS 4

**Infrastruktura (Docker Compose)**
- `app` (PHP-FPM), `nginx`, `frontend`, `mysql`, `redis`, `mailpit`

---

## 3. Arhitektura

### Backend — slojevit pristup
```
routes/api.php
  └── Http/Middleware/     → auth:sanctum, subscription.active, subscription.feature
      └── Http/Controllers/ → validacija + HTTP odgovor
          └── Services/     → poslovna logika
              └── Models/   → Eloquent + relacije
```

**Kontroleri (15):** Analytics, Auth, Club, EventInvitation, MatchDay, Onboarding, Player, Role, Season, SuperAdmin, Team, TrainingSession, User, Api/Payment

**Servisi (8):** Auth, EventInvitation, Onboarding, **PlayerStats**, SubscriptionPlan, Team, TrainingSession, User

**Policies (4):** GameMatch, Team, TrainingSession, User

**Modeli (16):** Academy, Attendance, Club, GameMatch, MatchDay, Permission, Player, PlayerPayment, PlayerProfile, Role, Season, Subscription, SubscriptionPlan, Team, TrainingSession, User

### Frontend — feature-based organizacija
```
src/
├── features/     → admin, analytics, auth, calendar, dashboard, matches,
│                   onboarding, players, tactics, teams, trainings, users
├── views/        → 18 route komponenti
├── stores/       → auth, team, training, user (Pinia)
├── services/     → api, matchesService, paymentsService, playersService, teamsService
└── components/   → deljene komponente (CustomDatePicker, Navbar, ThemeToggle)
```

Svaki `feature/` folder prati obrazac: `*Page.vue` + `components/` + `composables/`.

---

## 4. Implementirane funkcionalnosti

| Modul | Status | Opis |
|---|---|---|
| **Autentifikacija** | ✅ | Registracija, login, logout, `/me` (Sanctum) |
| **Role & permisije** | ✅ | Dinamička registracija Gate-ova iz baze, SuperAdmin |
| **Klubovi** | ✅ | Profil kluba, multi-tenant izolacija preko `club_id` |
| **Timovi** | ✅ | CRUD, članovi, detalji tima, roster |
| **Igrači** | ✅ | CRUD, profil, pozicije, seniority iz `age_group`, foto upload |
| **Treninzi** | ✅ | CRUD, evidencija prisustva, RSVP preko email pozivnica |
| **Utakmice** | ✅ | CRUD, zapisnik (sastav/golovi/asistencije), filter po mesecu |
| **Analitika** | ✅ | Indeks korisnosti, prosečan odaziv, pretraga igrača, filteri |
| **Kalendar** | ✅ | Mesečni prikaz događaja, CustomDatePicker |
| **Finansije** | ✅ | Uplate igrača, bulk unos, pregled po timu |
| **Pretplate** | ✅ | 5 paketa, feature-gating, SuperAdmin upravljanje |
| **Onboarding** | ✅ | Registracija kluba preko tokena, izbor paketa |
| **Notifikacije** | ✅ | Email pozivnice sa potpisanim RSVP linkovima (Mailpit) |
| **Taktika** | 🟡 | Postoji stranica, minimalna funkcionalnost |

### Frontend rute (16)
`/` · `/users` · `/players` · `/players/:id` · `/teams` · `/teams/:id` · `/trainings` · `/matches` · `/calendar` · `/analytics` · `/finances` · `/tactics` · `/super-admin` · `/onboarding` · `/rsvp-confirmation` · `/subscription-pending`

---

## 5. Model podataka — trenutno stanje baze

| Tabela | Redova | Napomena |
|---|---|---|
| `clubs` | 1 | |
| `users` | 2 | Nalozi (admini), **ne igrači** |
| `teams` | 4 | |
| `players` | 60 | Stvarni roster |
| `match_days` | 3 | |
| `match_day_player` | 30 | Zapisnici utakmica |
| `training_sessions` | 1 | |
| `event_invitations` | 90 | RSVP (polimorfno: trening/utakmica) |
| `player_payments` | 60 | |
| `subscriptions` | 1 | Premium, aktivna |

### Ključni koncept: `users` vs `players`
Ovo je **najvažnija distinkcija u modelu podataka**:
- `users` = nalozi za prijavu (admini, treneri) — trenutno 2
- `players` = igrači u rosteru — trenutno 60
- **Ne postoji veza `players.user_id`** — to su odvojeni entiteti

Sve funkcionalnosti vezane za igrače (prisustvo, zapisnik, statistika) moraju ići preko `players`, ne `users`. Nekoliko bagova ispravljenih u ovoj sesiji poticalo je upravo iz mešanja ova dva entiteta.

---

## 6. Ispravljeni bagovi (ova sesija)

### 6.1 Analitika — "Prosečan odaziv" je uvek bio 0%
**Uzrok (tri sloja):**
1. `AnalyticsController` je čitao `training_user.user_id` preko `$player->user_id ?? $player->id` — ali `players` tabela **nema** `user_id` kolonu, pa je `players.id` spajan sa `users.id` (60 vs 2 reda, 0 preklapanja).
2. `training_user` tabela je imala **0 redova**. Stvarno prisustvo je u `event_invitations`.
3. `syncAttendance` je zvao `sync($ids)` bez pivot podataka → `attended` je ostajao `false`.

**Rešenje:** agregacija iz `event_invitations` za završene treninge, grupisani upiti (2 upita umesto 120 — N+1), `sync()` sada upisuje `['attended' => true]`.

**Rezultat:** `0%` → `80%`, metrika se menja pri promeni prisustva.

---

### 6.2 Zapisnik utakmice — "Sastav (0)"
**Uzrok:**
1. Backend je eager-load-ovao `team.members`, frontend čitao `team.users || team.players` → nijedan ključ nije postojao.
2. `MatchDay::players()` je bio vezan za `User` preko `match_day_user` — `team_user` ima 4 reda (sve isti admin), nijedan igrač.
3. RSVP statusi iz `event_invitations` se nisu čitali.

**Rešenje:** nova pivot tabela `match_day_player` (vezana za `players`), eager loading `team.players` + `players` + `invitedPlayers`, frontend gradi sastav iz rostera i **pre-čekira igrače koji su potvrdili dolazak**.

**Rezultat:** `Sastav (0)` → `Sastav (6)`, 15 igrača u listi.

---

### 6.3 Statistika igrača se nije ažurirala
**Uzrok:** zapisnik je upisivao golove/asistencije samo u pivot tabelu. Kartica igrača čita `players.matches_played/goals/assists` — te kolone niko nije ažurirao osim ručne izmene.

**Rešenje:** novi `PlayerStatsService` koji preračunava statistiku iz zapisnika **odigranih** utakmica (`status='completed'` **i** `attended=1`). Poziva se na:
- čuvanje zapisnika (uključujući igrače uklonjene iz sastava)
- promenu statusa utakmice
- brisanje utakmice

Plus migracija za jednokratnu sinhronizaciju postojećih podataka.

**Odluka:** zapisnici su **jedini izvor istine**. Polja `matches_played`/`goals`/`assists` su uklonjena iz `PlayerController@update` validacije i zaključana u UI-u, da ručni unos ne bi pravio nesklad.

---

### 6.4 Dodate funkcionalnosti
- **Pretraga igrača** na `/analytics` — po imenu, poziciji, broju dresa i timu, sa normalizacijom dijakritika (`milos` → Miloš, `djordje` → Đorđe)
- **Filter po mesecu** na `/matches` — važi za oba taba, opcije se grade iz postojećih podataka, sortirano najnovije prvo

---

## 7. Uočeni problemi i tehnički dug

### 🔴 Visok prioritet

**7.1 Neispravni feature slug-ovi u paketima**
Paketi `Pro Academy` i `Unlimited` koriste tačkastu notaciju koja se ne poklapa sa onim što rute zahtevaju:

| Paket | Ima | Rute traže | Rezultat |
|---|---|---|---|
| `pro` | `club.profile` | `club_profile` | ❌ 403 |
| `unlimited` | `club.profile`, `advanced.statistics` | `club_profile`, `advanced_stats` | ❌ 403 |

**Posledica:** klub na *Unlimited* paketu (najskuplji, 100) **ne može pristupiti** `/analytics` ni profilu kluba. Trenutno neprimetno jer jedini klub koristi *Premium*.

**7.2 Redundantne tabele za isti koncept**
Prisustvo na treninzima ima **tri** tabele: `attendance` (0), `attendances` (0), `training_user` (0) — sve prazne, stvarni podaci su u `event_invitations`. Slično, `match_day_user` (0) je zamenjen sa `match_day_player` (30).

### 🟡 Srednji prioritet

**7.3 Mrtav kod**
- `CheckSubscriptionFeature.php` — nije registrovan (koristi se `EnsureSubscriptionFeature`)
- `GameMatch` model + `GameMatchPolicy` — zamenjen `MatchDay`-em; `Player::matches()` pokazuje na nepostojeću `match_player` tabelu
- `PlayerProfile` (0 redova), `Attendance` modeli
- `tasks`, `comments`, `seasons` tabele — prazne, ostatak originalnog projekta

**7.4 Test pokrivenost**
32 testa pokrivaju uglavnom auth, role, korisnike i pretplate. **Nema testova** za: analitiku (agregacije), zapisnik utakmice, `PlayerStatsService`, prisustvo na treninzima — tj. baš za logiku gde su nađena sva tri baga.

**7.5 Zastareo README**
Opisuje pogrešan domen (task management), navodi neimplementirane module.

### ⚪ Nizak prioritet

- Nema keširanja — analitika računa agregacije pri svakom zahtevu (u redu za trenutni obim)
- Dupli git identitet (`Zarko Gavric` / `zarkog1993`)
- Stanje filtera na `/analytics` je modul-level → pamti se pri navigaciji
- Otkazane utakmice (`canceled`) se ne prikazuju ni u jednom tabu

---

## 8. Preporučeni sledeći koraci

1. **Uskladiti feature slug-ove** u `subscription_plans` (`club.profile` → `club_profile`, `advanced.statistics` → `advanced_stats`) — blokira plaćene korisnike
2. **Dodati testove** za `PlayerStatsService`, analitiku i zapisnik utakmice — pokriti regresije iz ove sesije
3. **Obrisati mrtve tabele i modele** (`attendance`, `attendances`, `training_user`, `match_day_user`, `tasks`, `comments`, `GameMatch`)
4. **Ažurirati README** da odražava stvarni domen
5. Razmotriti vezu `players.user_id` ako igrači ikada budu imali naloge za prijavu

---

## 9. Zaključak

Projekat je **funkcionalno bogat i arhitektonski solidan** — čista slojevita struktura na backendu, dosledna feature-based organizacija na frontendu, multi-tenant izolacija i radni sistem pretplata. Za tri meseca razvoja obim je značajan.

Glavni rizik nije arhitektura nego **evolucioni dug**: projekat je menjao domen (task manager → sportski SaaS) i model igrača (`users` → `players`), a stari slojevi nisu uklanjani. Sva tri baga ispravljena u ovoj sesiji imala su **isti koren** — kod koji je gađao `users`/`training_user` umesto `players`/`event_invitations`.

Čišćenje redundantnih tabela i popravka feature slug-ova bi uklonili najveći deo tog rizika.
