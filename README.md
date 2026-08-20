# TaskFlow

TaskFlow je REST API aplikacija za upravljanje zadacima, izgrađena na Laravel-u i pakovana u Docker okruženje. Autentifikacija se obavlja pomoću Laravel Sanctum bearer tokena.

## Sadržaj

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Arhitektura](#arhitektura)
- [Installation](#installation)
- [Konfiguracija okruženja](#konfiguracija-okruženja)
- [Pokretanje](#pokretanje)
- [Testiranje](#testiranje)
- [API](#api)
- [License](#license)

## Features

Trenutno implementirano:

- **Authentication** — registracija, prijava, odjava i dohvat trenutnog korisnika (Sanctum bearer token)

Planirano (još nije implementirano):

- Users
- Tasks
- Roles
- Comments
- Notifications

## Tech Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum (token autentifikacija)
- MySQL 8.0
- Redis 7
- Docker / Docker Compose
- Nginx
- MailHog (hvatanje e-pošte u razvoju)
- PHPUnit

## Arhitektura

Aplikacija koristi slojevit pristup — kontroleri su tanki i delegiraju logiku servisima:

- `app/Http/Controllers/AuthController.php` — HTTP ulazna tačka za autentifikaciju
- `app/Http/Requests/` — validacija ulaznih podataka (`RegisterRequest`, `LoginRequest`)
- `app/Http/Resources/AuthResource.php` — oblikovanje JSON odgovora
- `app/Services/AuthService.php` — poslovna logika (kreiranje korisnika, izdavanje/brisanje tokena)
- `app/Models/User.php` — Eloquent model korisnika

Docker Compose pokreće servise: `app` (PHP-FPM), `nginx`, `mysql`, `redis` i `mailhog`.

## Installation

Preduslovi: Docker i Docker Compose.

1. Kloniraj repozitorijum i uđi u direktorijum projekta:

   ```bash
   git clone <repo-url> taskflow
   cd taskflow
   ```

2. Podigni Docker kontejnere:

   ```bash
   docker compose up -d --build
   ```

3. Instaliraj PHP zavisnosti unutar `app` kontejnera:

   ```bash
   docker compose exec app composer install
   ```

4. Kreiraj `.env` fajl i generiši aplikacijski ključ:

   ```bash
   docker compose exec app cp .env.example .env
   docker compose exec app php artisan key:generate
   ```

5. Pokreni migracije baze:

   ```bash
   docker compose exec app php artisan migrate
   ```

## Konfiguracija okruženja

Podrazumevane vrednosti baze (iz `docker-compose.yml`) — uskladi ih sa `.env`:

| Ključ           | Vrednost        |
| --------------- | --------------- |
| `DB_CONNECTION` | `mysql`         |
| `DB_HOST`       | `mysql`         |
| `DB_PORT`       | `3306`          |
| `DB_DATABASE`   | `taskflow_app`  |
| `DB_USERNAME`   | `taskflow`      |
| `DB_PASSWORD`   | `secret`        |
| `REDIS_HOST`    | `redis`         |
| `REDIS_PORT`    | `6379`          |
| `MAIL_HOST`     | `mailhog`       |
| `MAIL_PORT`     | `1025`          |

Napomena: `DB_HOST`, `REDIS_HOST` i `MAIL_HOST` koriste imena Docker servisa, a ne `localhost`.

## Pokretanje

Nakon što su kontejneri pokrenuti, servisi su dostupni na:

| Servis           | URL / Port             |
| ---------------- | ---------------------- |
| API (Nginx)      | http://localhost:8080  |
| MySQL            | `localhost:3306`       |
| Redis            | `localhost:6379`       |
| MailHog (SMTP)   | `localhost:1025`       |
| MailHog (Web UI) | http://localhost:8025  |

## Testiranje

Pokreni test skup (PHPUnit) unutar `app` kontejnera:

```bash
docker compose exec app php artisan test
```

## API

Osnovni URL: `http://localhost:8080/api`

Svi odgovori su u JSON formatu. Zaštićene rute zahtevaju zaglavlje:

```
Authorization: Bearer <access_token>
```

### `POST /api/register`

Registruje novog korisnika i vraća bearer token.

**Telo zahteva:**

```json
{
  "name": "Djura",
  "email": "djura@example.com",
  "password": "TajnaLozinka123",
  "password_confirmation": "TajnaLozinka123"
}
```

Validaciona pravila:

- `name` — obavezno, string, maks. 255 znakova
- `email` — obavezno, validan email, jedinstven u `users`
- `password` — obavezno, potvrđeno (`password_confirmation`), podrazumevana Laravel pravila jačine

**Odgovor `201 Created`:**

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Djura",
      "email": "djura@example.com"
    },
    "access_token": "1|abcdef...",
    "token_type": "Bearer"
  }
}
```

Greška `422` ako email već postoji ili validacija ne prođe.

### `POST /api/login`

Prijavljuje korisnika i vraća novi bearer token.

**Telo zahteva:**

```json
{
  "email": "djura@example.com",
  "password": "TajnaLozinka123"
}
```

**Odgovor `200 OK`:** identična struktura kao kod registracije (`data.user`, `data.access_token`, `data.token_type`).

Greška `422` sa porukom `Podaci za prijavu nisu ispravni.` ako kredencijali nisu tačni.

### `GET /api/me`

Vraća trenutno autentifikovanog korisnika. **Zahteva token.**

**Odgovor `200 OK`:**

```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Djura",
      "email": "djura@example.com"
    },
    "access_token": null,
    "token_type": "Bearer"
  }
}
```

### `POST /api/logout`

Poništava (briše) token korišćen u trenutnom zahtevu. **Zahteva token.**

**Odgovor `200 OK`:**

```json
{
  "message": "Uspešno ste se odjavili."
}
```

### `GET /api/user`

Vraća sirovi model autentifikovanog korisnika (Sanctum). **Zahteva token.**

## License

MIT