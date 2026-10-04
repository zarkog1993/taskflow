# TaskFlow

TaskFlow je SaaS platforma za upravljanje fudbalskim klubovima — timovi, igrači, treninzi, utakmice, statistika i finansije. Backend je Laravel REST API (Sanctum token autentifikacija), frontend je Vue 3 SPA, a celo okruženje se podiže preko Docker Compose-a.

> Ime „TaskFlow" je nasleđeno iz rane faze projekta kada je aplikacija bila task manager. Domen je u međuvremenu u potpunosti prešao na upravljanje sportskim klubom.

## Sadržaj

- [Funkcionalnosti](#funkcionalnosti)
- [Tech Stack](#tech-stack)
- [Model podataka](#model-podataka)
- [Arhitektura](#arhitektura)
- [Instalacija](#instalacija)
- [Konfiguracija okruženja](#konfiguracija-okruženja)
- [Pokretanje](#pokretanje)
- [Pretplate i kontrola pristupa](#pretplate-i-kontrola-pristupa)
- [Testiranje](#testiranje)
- [API](#api)
- [Poznati nedovršeni delovi](#poznati-nedovršeni-delovi)

## Funkcionalnosti

| Modul | Opis |
| --- | --- |
| **Autentifikacija** | Registracija, prijava, odjava, dohvat trenutnog korisnika (Sanctum bearer token) |
| **Onboarding** | Registracija kluba preko pozivnog tokena i izbor pretplatničkog paketa |
| **Klub** | Profil kluba i osnovni podaci |
| **Timovi** | Starosne kategorije, roster igrača, članovi stručnog štaba |
| **Igrači** | Kartoni igrača, pozicije, brojevi dresova, fotografije, statistika |
| **Treninzi** | Zakazivanje termina, slanje pozivnica, RSVP odgovori igrača |
| **Utakmice** | Zakazivanje, zasebni tabovi za zakazane/odigrane/otkazane mečeve; Premium zapisnik sa sastavom, golovima i asistencijama |
| **Statistika** | Automatsko računanje odigranih utakmica, golova i asistencija iz zapisnika |
| **Analitika** | Prosečna posećenost treninga, top strelci, filteri po timu i periodu, pretraga igrača |
| **Finansije** | Evidencija uplata igrača, pregled po timu, grupne uplate |
| **Super admin** | Upravljanje klubovima, korisnicima i pretplatama |
| **Taktika** | Formacije, drag-and-drop raspored i čuvanje jedne aktivne taktike po ekipi |

## Tech Stack

**Backend**

- PHP 8.3+
- Laravel 13.8
- Laravel Sanctum 4 (token autentifikacija)
- MySQL 8.0
- Redis 7
- PHPUnit

**Frontend**

- Vue 3.5 (Composition API, `<script setup>`)
- Vite 8
- Pinia 4 (state management)
- Vue Router 4
- Tailwind CSS 4
- Axios

**Infrastruktura**

- Docker / Docker Compose
- Nginx
- Mailpit (hvatanje e-pošte u razvoju)

## Model podataka

Ključna stvar za razumevanje projekta: **`users` i `players` su dve odvojene tabele bez veze među sobom.**

- **`users`** — nalozi za prijavu u aplikaciju (administratori kluba, treneri, super admin). Imaju email i lozinku.
- **`players`** — roster igrača kluba. Nemaju nalog, ne prijavljuju se, i **nemaju `user_id` kolonu**.

Sva evidencija vezana za igrače (pozivnice, prisustvo, sastavi, statistika, uplate) referencira `players`, nikada `users`.

### Glavne tabele

| Tabela | Uloga |
| --- | --- |
| `clubs` | Klub — korenski entitet, nosilac izolacije podataka |
| `users` | Nalozi za prijavu, vezani za klub preko `club_id` |
| `teams` | Starosne kategorije unutar kluba |
| `players` | Roster igrača, vezan za tim preko `team_id` |
| `training_sessions` | Termini treninga |
| `match_days` | Utakmice |
| `event_invitations` | Polimorfna tabela pozivnica i RSVP odgovora (`invitable_type` ∈ `training`, `match`), ključana po `player_id` |
| `match_day_player` | Sastav utakmice — pivot sa `attended`, `goals`, `assists` |
| `player_payments` | Uplate igrača |
| `subscriptions`, `subscription_plans` | Pretplata kluba i dostupne funkcionalnosti |

### Prisustvo i RSVP

Prisustvo se vodi **isključivo preko `event_invitations`**. Kada se zakaže trening ili utakmica, igračima se šalju email pozivnice sa potpisanim linkovima (`invitations.rsvp`), a njihov odgovor (`accepted` / `declined`) se upisuje u pivot. Analitika računa posećenost iz ove tabele.

### Statistika igrača

Zapisnik utakmice je **jedini izvor istine** za `matches_played`, `goals` i `assists`. `PlayerStatsService` ponovo izračunava ove vrednosti pri svakoj izmeni zapisnika, promeni statusa utakmice ili brisanju utakmice. Broje se samo utakmice sa statusom `completed` gde je igrač označen kao prisutan u sastavu. Ova polja se zato ne mogu ručno menjati kroz API.

## Arhitektura

Aplikacija koristi slojevit pristup — kontroleri su tanki i delegiraju logiku servisima.

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # HTTP ulazne tačke
│   │   ├── Requests/       # Validacija ulaznih podataka
│   │   ├── Resources/      # Oblikovanje JSON odgovora
│   │   └── Middleware/     # EnsureSubscriptionFeature (kontrola pristupa)
│   ├── Models/             # Eloquent modeli
│   ├── Services/           # Poslovna logika
│   ├── Policies/           # Autorizacija
│   └── Notifications/      # Email pozivnice
├── database/migrations/
├── routes/api.php
└── tests/
```

**Servisi:** `AuthService`, `UserService`, `TeamService`, `TrainingSessionService`, `EventInvitationService`, `PlayerStatsService`, `OnboardingService`, `SubscriptionPlanService`.

### Izolacija podataka

Model `Player` ima globalni scope `club_isolation` koji automatski ograničava upite na klub prijavljenog korisnika. U servisnom i CLI kontekstu (gde nema `request()`) ovaj scope se mora eksplicitno zaobići sa `withoutGlobalScopes()` — `PlayerStatsService` to i radi.

### Frontend

```
taskflow-frontend/src/
├── features/        # admin, analytics, auth, calendar, dashboard,
│                    # matches, onboarding, players, tactics, teams,
│                    # trainings, users
├── composables/     # deljena logika (npr. useMatchStats)
├── stores/          # Pinia store-ovi
├── components/      # deljene komponente
└── router/
```

Svaki `feature` direktorijum sadrži stranicu (`*Page.vue`), svoje `components/`, `composables/` i po potrebi `utils/`.

## Instalacija

Preduslovi: Docker i Docker Compose.

1. Kloniraj repozitorijum:

   ```bash
   git clone <repo-url> taskflow
   cd taskflow
   ```

2. Podigni kontejnere:

   ```bash
   docker compose up -d --build
   ```

3. Instaliraj PHP zavisnosti:

   ```bash
   docker compose exec -w /var/www/html/src app composer install
   ```

4. Kreiraj `.env` i generiši aplikacijski ključ:

   ```bash
   docker compose exec -w /var/www/html/src app cp .env.example .env
   docker compose exec -w /var/www/html/src app php artisan key:generate
   ```

5. Pokreni migracije:

   ```bash
   docker compose exec -w /var/www/html/src app php artisan migrate
   ```

6. Po potrebi dodaj početne uloge i testne podatke. `DatabaseSeeder` pravi test nalog, pa ga ne pokreći na produkciji:

   ```bash
   docker compose exec -w /var/www/html/src app php artisan db:seed --class=RolesAndPermissionsSeeder
   docker compose exec -w /var/www/html/src app php artisan db:seed --class=TeamPlayersSeeder
   ```

`TeamPlayersSeeder` zahteva postojeći klub i tim; dodaje testne igrače i uplate prvom klubu. Seed-eri nisu pozvani iz `DatabaseSeeder`.

> **Napomena:** kod aplikacije se unutar `app` kontejnera nalazi na putanji `/var/www/html/src`, pa svaka `artisan` i `composer` komanda zahteva `-w /var/www/html/src`.

## Konfiguracija okruženja

Podrazumevane vrednosti (iz `docker-compose.yml`) — uskladi ih sa `.env`:

| Ključ | Vrednost |
| --- | --- |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `mysql` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `taskflow_app` |
| `DB_USERNAME` | `taskflow` |
| `DB_PASSWORD` | `secret` |
| `REDIS_HOST` | `redis` |
| `REDIS_PORT` | `6379` |
| `MAIL_HOST` | `mailpit` |
| `MAIL_PORT` | `1025` |
| `FRONTEND_URL` | `http://localhost:5173` |

`DB_HOST`, `REDIS_HOST` i `MAIL_HOST` koriste imena Docker servisa, a ne `localhost`.

`FRONTEND_URL` se koristi za preusmeravanje nakon RSVP odgovora iz email pozivnice.

Frontend koristi `VITE_API_BASE_URL` za adresu backend API-ja. Ako promenljiva nije postavljena, koristi se `http://localhost:8080/api`. Za drugo okruženje je postavi u `taskflow-frontend/.env` ili u promenljive okruženja prilikom Vite build-a, na primer:

```env
VITE_API_BASE_URL=https://api.example.com/api
```

Pošto se Vite promenljive ugrađuju u frontend bundle, promena vrednosti zahteva ponovno pokretanje development servera ili novi build.

## Pokretanje

| Servis | URL / Port |
| --- | --- |
| API (Nginx) | http://localhost:8080 |
| Frontend (Vite) | http://localhost:5173 |
| MySQL | `localhost:3306` |
| Redis | `localhost:6379` |
| Mailpit (SMTP) | `localhost:1025` |
| Mailpit (Web UI) | http://localhost:8025 |

Produkcijski build frontenda:

```bash
cd taskflow-frontend
npm run build
```

## Pretplate i kontrola pristupa

Svaki klub ima pretplatu koja nosi listu dozvoljenih funkcionalnosti. Middleware `subscription.feature:<slug>` (klasa `EnsureSubscriptionFeature`) štiti rute i vraća `403` ako paket ne sadrži traženu funkcionalnost.

| Paket | Cena | Funkcionalnosti |
| --- | --- | --- |
| `basic` | 10 €/mesec | `club_profile`, `players`, `teams` |
| `standard` | 20 €/mesec | `club_profile`, `players`, `teams`, `matches`, `news` |
| `premium` | 50 €/mesec | `standard` + `advanced_stats`, `tactics` |

Slug `teams` otključava i timove i treninge — rute `/training-sessions` su gate-ovane istim slugom. Utakmice (`matches`) su prva razlika između `basic` i `standard` paketa.

U ponudi su Basic, Standard i Premium paketi. Stari `pro` i `unlimited` zapisi ostaju neaktivni za nove prijave, ali se postojeće pretplate mogu administrirati. Slugovi koriste notaciju sa donjom crtom (`club_profile`, `advanced_stats`). Backend trenutno proverava `club_profile`, `players`, `teams`, `matches` i `advanced_stats`; frontend ruter dodatno proverava `tactics` i `advanced_management`. Nijedan aktivni paket trenutno ne sadrži `advanced_management`; `news`, `club_basic_information` i `additional_content` su opisne oznake bez implementiranog feature-gating-a.

**Važno:** `subscriptions` tabela nosi sopstvenu kopiju liste funkcionalnosti — `Subscription::hasFeature()` čita `subscriptions.features`, a ne plan. Izmena paketa se zato ne odražava na postojeće pretplatnike dok im se lista ponovo ne prepiše iz plana.

Aktivni katalog paketa čita se iz `subscription_plans`; GET zahtevi ga ne sinhronizuju niti menjaju. Migracije postavljaju početne pakete, a `pro` i `unlimited` ostaju neaktivni zbog postojećih pretplata i istorije.

## Testiranje

```bash
docker compose exec -T -w /var/www/html/src app php artisan test
```

Trenutno stanje: **51 test, 191 asercija.**

## API

Podrazumevani API URL: `http://localhost:8080/api` (može se promeniti pomoću `VITE_API_BASE_URL`, vidi [konfiguraciju okruženja](#konfiguracija-okruženja)).

Svi odgovori su u JSON formatu. Zaštićene rute zahtevaju zaglavlje `Authorization: Bearer <token>`.

### Javne rute

| Metoda | Ruta | Opis |
| --- | --- | --- |
| `POST` | `/register` | Registracija korisnika, vraća bearer token |
| `POST` | `/login` | Prijava, vraća bearer token |
| `GET` | `/subscription-plans` | Lista dostupnih paketa |
| `GET` | `/onboarding/{token}` | Podaci o pozivnici za registraciju kluba |
| `POST` | `/onboarding/{token}/select` | Izbor paketa |
| `GET` | `/invitations/{type}/{event}/{player}/{status}` | RSVP odgovor iz email pozivnice (potpisana ruta) |

### Zaštićene rute

| Metoda | Ruta | Opis |
| --- | --- | --- |
| `GET` | `/me` | Trenutni korisnik |
| `GET` | `/user` | Alias za dohvat trenutno prijavljenog korisnika |
| `POST` | `/logout` | Poništavanje tokena |
| `POST` | `/onboarding/complete` | Izbor paketa za registrovani klub |
| `GET` `POST` | `/teams` | Lista i kreiranje timova |
| `GET` `PUT` `DELETE` | `/teams/{team}` | Detalji, izmena, brisanje tima |
| `GET` `PUT` | `/teams/{team}/tactics` | Učitavanje i čuvanje taktike ekipe (Premium) |
| `GET` `POST` | `/players` | Lista i kreiranje igrača |
| `GET` `PUT` `PATCH` `DELETE` | `/players/{player}` | Detalji, izmena i brisanje igrača |
| `GET` `POST` | `/training-sessions` | Lista i zakazivanje treninga |
| `PUT` `DELETE` | `/training-sessions/{trainingSession}` | Izmena i brisanje treninga |
| `PUT` | `/training-sessions/{trainingSession}/status` | Promena statusa treninga |
| `GET` `POST` | `/matches` | Lista i zakazivanje utakmica |
| `PUT` | `/matches/{match}` | Izmena utakmice |
| `DELETE` | `/matches/{match}` | Brisanje utakmice |
| `PUT` | `/matches/{match}/stats` | Čuvanje zapisnika (Premium: sastav, golovi, asistencije) |
| `PUT` | `/matches/{match}/status` | Promena statusa utakmice |
| `GET` | `/analytics` | Analitika (podržava `team_id`, `date_from`, `date_to`) |
| `GET` | `/finances/overview` | Pregled finansija |
| `GET` | `/teams/{team}/payments` | Uplate za tim |
| `POST` | `/payments` | Evidentiranje ili izmena uplate |
| `POST` | `/payments/bulk` | Grupno evidentiranje uplata |
| `GET` `PUT` | `/club` | Profil kluba |
| `GET` `POST` | `/users` | Lista i kreiranje naloga |
| `GET` `PUT` `PATCH` `DELETE` | `/users/{user}` | Detalji, izmena i brisanje naloga |
| `PATCH` | `/subscriptions/{subscription}/status` | Odluka super-admina o pretplati |
| `PATCH` | `/super-admin/subscriptions/{subscription}/plan` | Promena paketa |
| `PATCH` | `/super-admin/subscriptions/{subscription}/cancel` | Otkazivanje pretplate |
| `DELETE` | `/super-admin/users/{user}` | Brisanje korisnika od super-admina |
| `DELETE` | `/super-admin/clubs/{club}` | Brisanje kluba od super-admina |
| `GET` | `/roles` | Lista uloga |
| `GET` | `/super-admin/dashboard` | Super admin pregled |
| `GET` | `/admin/clubs` | Pregled klubova za super-admina |

Kompletnu listu ruta ispisuje:

```bash
docker compose exec -w /var/www/html/src app php artisan route:list --path=api
```

## Poznati nedovršeni delovi

- CI workflow pokreće Laravel testove, ali ne proverava frontend build.

## Licenca

Projekat nema definisanu licencu.
