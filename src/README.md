# 🚀 TaskFlow API

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/badge/Tests-37%20Passed-brightgreen?style=for-the-badge&logo=github-actions&logoColor=white)]()

TaskFlow API je moderan, skalabilan i čist RESTful backend sistem namenjen upravljanju projektima, zadacima, timovima, ulogama i komentarima u realnom vremenu. Aplikacija je razvijena u skladu sa najboljim inženjerskim praksama (Clean Code, Service-Repository pattern, DTO, Form Requests, Custom RBAC, Event-driven Notifications) i poseduje 100% pokrivenost automatizovanim testovima.

---

## 🛠️ Tehnički Stak (Tech Stack)

* **Framework:** Laravel 11.x
* **Jezik:** PHP 8.2+
* **Baza Podataka:** MySQL / PostgreSQL / SQLite
* **Autentifikacija:** Laravel Sanctum (Bearer Tokens)
* **Autorizacija:** Laravel Gates & Policies + Custom RBAC (Role-Based Access Control)
* **Testiranje:** PHPUnit / Pest (37/37 Feature & Unit Testova)
* **Arhitektura:** Controller-Service Pattern, Form Request Validation, API Resources

---

## ✨ Detaljan Opis Modula i Funkcionalnosti

### 1. 🔑 Autentifikacija i Profiling (Auth Module)
* **Sigurna Registracija & Prijava:** Korisnici se registruju uz automatsku validaciju i lozinke keširane preko bcrypt/argon2 algorithm-a.
* **Token Management (Sanctum):** Prilikom prijave generiše se unikatni Bearer Token (`personal_access_tokens`).
* **Session Control:** Podrška za odjavu sa trenutnog uređaja (`/logout`) ili poništavanje svih aktivnih sesija.

### 2. 📋 Upravljanje Zadacima (Task Management)
* **CRUD Operacije:** Kreiranje, pregled, izmena i brisanje zadataka.
* **Filtriranje i Pretraga:** Dinamičko filtriranje zadataka po statusu (`todo`, `in_progress`, `done`) i prioritetu (`low`, `medium`, `high`).
* **Dodeljivanje Zadataka (Assignment):** Svaki zadatak ima kreatora (`user_id`) i opciono dodeljenog člana tima (`assigned_to`).
* **Granularna Autorizacija:** Implementiran `TaskPolicy` koji garantuje da zadatak mogu menjati samo kreator ili dodeljeni korisnik, dok samo kreator može obrisati zadatak.

### 3. 💬 Sistem Komentara (Comments System)
* **Ugnježđene Rute (Nested Resources):** Komentari su vezani za specifične zadatke (`/api/tasks/{task}/comments`).
* **Diskutabilni Pop-up:** Pravo ostavljanja komentara imaju isključivo članovi tima povezani sa tim zadatkom.
* **Prava Autora:** Korisnik može menjati i brisati isključivo sopstvene komentare (`CommentPolicy`).

### 4. 🛡️ Uloge i Permisije (Custom M:N RBAC)
* **Implementacija od nule:** Bez eksternih paketa, realizovane 4 tabele u bazi (`roles`, `permissions`, `role_user`, `permission_role`).
* **Pomoćne Metode na Modelu:** `$user->hasRole('admin')` i `$user->hasPermission('manage-tasks')`.
* **Gate Integracija:** Uloge i permisije su dinamički mapirane u `AppServiceProvider`-u kroz `Gate::define()`.

### 5. 🔔 Sistem Obaveštenja (Database Notifications)
* **Događaji u Realnom Vremenu:** Slanje obaveštenja u bazu prilikom:
  1. Dodeljivanja novog zadatka korisniku (`TaskAssignedNotification`).
  2. Dodavanja novog komentara na zadatku (`CommentAddedNotification`).
* **API za Notifikacije:** Rute za pregled nepročitanih notifikacija i označavanje pročitanim (`markAsRead`).

---

## 🏗️ Arhitektura Projekta

Aplikacija koristi strogo odvojene slojeve odgovornosti radi lakšeg održavanja, testiranja i skaliranja:

```text
app/
├── Http/
│   ├── Controllers/       # Tanki kontroleri koji primaju request i vraćaju HTTP response
│   ├── Requests/          # FormRequest klase za validaciju ulaznih podataka
│   └── Resources/         # API Resources za transformaciju i formatiranje JSON odziva
├── Models/                # Eloquent modeli sa opisanim relacijama i pomocnim metodama
├── Notifications/         # Database obaveštenja (TaskAssigned, CommentAdded)
├── Policies/              # Polise autorizacije (TaskPolicy, CommentPolicy)
├── Providers/             # Service Provideri (dinamička registracija permisija)
└── Services/              # Enkapsulirana biznis logika (TaskService, CommentService...)
```

## 🔌 API Endpoints Documentation

Sve rute koje zahtevaju autentifikaciju očekuju Header: `Authorization: Bearer <your_sanctum_token>`.

### 🔑 Autentifikacija (`/api`)
| Metoda | Ruta | Opis | Autorizacija |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Registracija novog korisnika | Javna |
| `POST` | `/api/login` | Prijava i generisanje Bearer tokena | Javna |
| `POST` | `/api/logout` | Odjava i revokacija aktivnog tokena | Sanctum |
| `GET` | `/api/me` | Prikaz profila ulogovanog korisnika | Sanctum |

### 👥 Korisnici (`/api/users`)
| Metoda | Ruta | Opis | Autorizacija |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/users` | Listanje svih korisnika (Paginisano) | Sanctum |
| `POST` | `/api/users` | Kreiranje novog korisnika od strane Admina | Admin |
| `GET` | `/api/users/{id}` | Prikaz pojedinačnog korisnika | Sanctum |
| `PUT` | `/api/users/{id}` | Ažuriranje korisničkog profila | Vlasnik / Admin |
| `DELETE` | `/api/users/{id}` | Brisanje korisničkog naloga | Admin |

### 📋 Zadaci (`/api/tasks`)
| Metoda | Ruta | Opis | Autorizacija |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/tasks` | Lista zadataka (podržava `?status=todo&priority=high`) | Sanctum |
| `POST` | `/api/tasks` | Kreiranje novog zadatka | Sanctum |
| `GET` | `/api/tasks/{id}` | Detaljni prikaz zadatka | TaskPolicy |
| `PUT` | `/api/tasks/{id}` | Izmena zadatka (status, opis, dodeljeni korisnik) | TaskPolicy |
| `DELETE` | `/api/tasks/{id}` | Brisanje zadatka | Kreator zadatka |

### 💬 Komentari (`/api/tasks/{task}/comments`)
| Metoda | Ruta | Opis | Autorizacija |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/tasks/{task}/comments` | Listanje svih komentara na zadatku | CommentPolicy |
| `POST` | `/api/tasks/{task}/comments` | Dodavanje novog komentara na zadatak | CommentPolicy |
| `PUT` | `/api/comments/{comment}` | Ažuriranje teksta komentara | Autor komentara |
| `DELETE` | `/api/comments/{comment}` | Brisanje komentara | Autor komentara |

### 🔔 Obaveštenja (`/api/notifications`)
| Metoda | Ruta | Opis | Autorizacija |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/notifications` | Lista svih obaveštenja ulogovanog korisnika | Sanctum |
| `GET` | `/api/notifications/unread` | Lista samo nepročitanih obaveštenja | Sanctum |
| `PATCH` | `/api/notifications/{id}/read` | Označavanje pojedinačnog obaveštenja kao pročitano | Sanctum |

---

## 🚀 Instalacija i Podešavanje (Setup Guide)

Pratite sledeće korake za lokalno podizanje i pokretanje projekta na vašoj mašini:

### 1. Kloniranje repozitorijuma
```bash
git clone [https://github.com/tvoj-username/taskflow-api.git](https://github.com/tvoj-username/taskflow-api.git)
cd taskflow-api