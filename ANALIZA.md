# TaskFlow — pregled projekta

> Ažurirano: 2026-10-04. Razvojna baza i njeni podaci nisu izvor dokumentacije.

## Pregled

TaskFlow je SaaS aplikacija za upravljanje fudbalskim klubovima. Backend je Laravel 13 REST API, frontend je Vue 3 SPA, a razvojno okruženje se podiže preko Docker Compose-a. Aplikacija obuhvata klubove, timove, roster igrača, treninge, utakmice, RSVP pozivnice, statistiku, finansije, pretplate i super-admin alatke.

Najvažnija domenska odluka: **`users` i `players` su različiti entiteti.** `users` su nalozi za prijavu (npr. klupski administratori i treneri); `players` su igrači u rosteru. Igrači trenutno nemaju naloge niti `user_id` vezu. Sastavi, RSVP, statistika i uplate zato referenciraju `players`.

## Tehnologije i struktura

- **Backend:** PHP 8.3+, Laravel 13, Sanctum, Eloquent, MySQL; Redis je uključen u Docker okruženje.
- **Frontend:** Vue 3, Vite, Pinia, Vue Router, Tailwind CSS 4 i Axios.
- **Testovi:** PHPUnit / Laravel test runner; trenutni skup ima 60 testova i 237 asercija.
- **CI:** GitHub Actions pokreće backend testove sa SQLite; frontend build nije deo workflow-a.

Backend je organizovan oko `routes/api.php`, HTTP kontrolera i middleware-a, modela, servisa, policies i API resources. Frontend je grupisan po funkcionalnosti u `taskflow-frontend/src/features/`, uz zajedničke stores, composables, services i komponente.

## Ključne domenske putanje

### Igrači i timovi

`Player` pripada klubu i opciono timu. Model ima globalni scope koji ograničava igrače na klub prijavljenog korisnika; super-admin može da vidi sve. Kreiranje igrača proverava aktivnu pretplatu i limit `max_players` na nivou celog kluba.

Nalozi u tabeli `users`, uključujući naloge sa ulogom `player`, nisu roster zapisi i ne troše `max_players`. Kreiranje naloga zahteva aktivnu pretplatu i feature pristup, dok se kvota primenjuje samo na `Player` zapise.

### Treninzi i RSVP

Pozivnice i odgovori igrača čuvaju se u polimorfnoj tabeli `event_invitations`, sa morph aliasima `training` i `match`. RSVP se obavlja preko potpisanog linka iz emaila. Analitika odaziv računa iz ovih zapisa.

### Utakmice i statistika

Utakmice su u `match_days`; zapisnik igrača je pivot `match_day_player` sa `attended`, `goals` i `assists`. `PlayerStatsService` izvodi statistiku igrača isključivo iz završenih utakmica u kojima je igrač označen kao prisutan. Zapisnik je dostupan Premium pretplatnicima; rute za utakmice su dostupne Standard i Premium paketima. Otkazane utakmice imaju zaseban tab u interfejsu.

### Pretplate

Novi klubovi biraju jedan od tri paketa: Basic (10 €/mesec), Standard (20 €/mesec) ili Premium (50 €/mesec). Paketi `pro` i `unlimited` ostaju neaktivni u bazi radi podrške postojećim pretplatama. Liste funkcionalnosti postoje i na planu i kao kopija na pretplati; odobravanje ili promena plana osvežava kopiju pretplate.

## Već rešeni problemi

Nedavne izmene su zatvorile nekoliko ranije identifikovanih problema:

- Pristup pojedinačnom korisničkom nalogu sada prolazi kroz policy; klupski admin ne može dodeliti super-admin ulogu niti prebaciti nov nalog u drugi klub.
- Dashboard koristi roster `players` za broj igrača i golove, umesto zastarele `users.player_profile` relacije.
- Limit `max_players` proverava samo stvarne `players` zapise na nivou kluba; nalozi sa ulogom `player` imaju zasebnu namenu.
- Konfiguracija paketa, onboarding i dokumentacija usklađeni su na tri paketa; Basic uključuje profil kluba, igrače i timove.
- Zapisnik utakmice je ograničen na Premium i podaci o statistici se ne učitavaju za pretplatnike bez `advanced_stats`.
- Otkazane utakmice prikazuju se odvojeno.
- Finansijske API rute zahtevaju aktivan `teams` feature; frontend API adresa se podešava preko `VITE_API_BASE_URL`.
- Taktika se čuva kao jedna aktivna formacija po ekipi; pristup je zaštićen `tactics` feature-om i proverom pristupa timu.
- Katalog pretplata se čita iz aktivnih zapisa u bazi bez menjanja podataka pri GET zahtevima; neaktivni `pro` i `unlimited` paketi ostaju dostupni za istorijske pretplate.
- Potpisani RSVP odgovori na trening ulaze u analitiku posećenosti; regresioni testovi proveravaju prihvaćene, odbijene i buduće pozivnice.
- Planer sastava za zakazane utakmice koristi samo potvrđene RSVP igrače i čuva predlog odvojeno od zvaničnog zapisnika.
- Zastarele tabele za task manager i stari modeli prisustva očišćeni su migracijama.

## Preporučeni sledeći koraci

1. **Ojačati klijentsku autentifikaciju.** Bearer token se čuva u `localStorage`; razmotriti HTTP-only cookie pristup ili odgovarajuće mitigacije XSS rizika pre javne produkcije.
2. **Uvesti keširanje tek uz merenje.** Analitika trenutno računa agregacije na zahtev; za trenutni obim prvo pratiti trajanje upita, pa keširati uz pouzdano invalidiranje nakon promene podataka.
3. **Uklanjati neaktivne modele postepeno.** Proveriti da li zastareli modeli i frontend template fajlovi imaju reference pre uklanjanja.

## Predlog interesantnog feature-a

**Dalje unapređenje planera sastava:** predložiti početnu postavu i rezerve na osnovu dostupnosti i pozicija, pa omogućiti treneru da pregleda i potvrdi predlog. Trenutni planer omogućava ručni izbor potvrđenih igrača i raspored na taktičkoj tabli.
