<template>
    <main class="overflow-hidden bg-[#f7f9f6] text-slate-900 dark:bg-[#07130f] dark:text-white">
        <section class="relative isolate">
            <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_75%_12%,rgba(34,197,94,0.14),transparent_38%)]"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-5 pb-20 pt-14 sm:px-8 sm:pt-20 lg:grid-cols-[0.95fr_1.05fr] lg:gap-10 lg:px-10 lg:pb-28 lg:pt-24">
                <div class="max-w-2xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-3.5 py-2 text-xs font-bold text-emerald-800 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Jedna platforma za ceo klub
                    </div>
                    <h1 class="text-4xl font-black leading-[1.08] tracking-tight sm:text-5xl lg:text-[4.25rem]">
                        Manje administracije.<br />
                        <span class="text-emerald-600 dark:text-emerald-400">Više igre.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300 sm:text-lg sm:leading-8">
                        Organizujte timove, igrače, treninge i utakmice na jednom mestu. ProTrainer pomaže vašem klubu da svaki dan na terenu bude bolje isplaniran.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <router-link to="/register" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3 text-sm font-extrabold text-[#062216] shadow-lg shadow-emerald-900/15 transition hover:-translate-y-0.5 hover:bg-emerald-400">
                            Pokreni klub
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </router-link>
                        <a href="#funkcionalnosti" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white/70 px-6 py-3 text-sm font-bold text-slate-800 transition hover:border-emerald-400 hover:bg-white dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 dark:hover:border-emerald-700 dark:hover:bg-slate-900">
                            Istraži platformu
                        </a>
                    </div>
                    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-2"><span class="text-emerald-600 dark:text-emerald-400">✓</span> Timovi i roster igrača</span>
                        <span class="flex items-center gap-2"><span class="text-emerald-600 dark:text-emerald-400">✓</span> Treninzi i utakmice</span>
                        <span class="flex items-center gap-2"><span class="text-emerald-600 dark:text-emerald-400">✓</span> Pregled statistike</span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl lg:ml-auto">
                    <div class="absolute -inset-5 rounded-[2rem] bg-emerald-400/15 blur-3xl"></div>
                    <div class="relative overflow-hidden rounded-[1.6rem] border border-white/10 bg-[#0d1e17] p-3 shadow-2xl shadow-emerald-950/25 sm:p-4">
                        <div class="flex items-center justify-between border-b border-white/10 px-2 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-400 text-sm font-black text-emerald-950">T</div>
                                <div>
                                    <p class="text-xs font-bold text-white">FK Zeleno Polje</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">Klupski pregled</p>
                                </div>
                            </div>
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-2.5 py-1 text-[10px] font-bold text-emerald-300">Primer prikaza</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5 py-3">
                            <div v-for="metric in metrics" :key="metric.label" class="rounded-xl border border-white/[0.07] bg-white/[0.04] p-3">
                                <p class="text-[10px] font-medium text-slate-400">{{ metric.label }}</p>
                                <p class="mt-2 text-xl font-black tracking-tight text-white">{{ metric.value }}</p>
                                <p class="mt-1 text-[9px] font-semibold text-emerald-300">{{ metric.note }}</p>
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-[1.2fr_0.8fr]">
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.04] p-3.5">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-xs font-bold text-white">Naredna aktivnost</p>
                                    <span class="text-[9px] font-semibold text-emerald-300">DANAS</span>
                                </div>
                                <div class="flex items-center gap-3 rounded-lg bg-white/[0.04] p-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-emerald-400/15 text-lg">⚽</div>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-bold text-white">Trening — Kadeti</p>
                                        <p class="mt-1 text-[10px] text-slate-400">18:30 · Glavni teren</p>
                                    </div>
                                    <span class="ml-auto shrink-0 rounded-md bg-emerald-400/10 px-2 py-1 text-[9px] font-bold text-emerald-300">12/16</span>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-[10px] text-slate-400">
                                    <span>Pozvani igrači</span><span class="font-bold text-white">16 igrača</span>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full w-3/4 rounded-full bg-emerald-400"></div></div>
                            </div>
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.04] p-3.5">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-xs font-bold text-white">Sledeća utakmica</p>
                                    <span class="text-[9px] text-slate-400">SUB, 11:00</span>
                                </div>
                                <div class="flex h-[100px] items-center justify-center gap-3 rounded-lg bg-[linear-gradient(135deg,rgba(34,197,94,0.13),rgba(255,255,255,0.02))]">
                                    <div class="text-center"><div class="mx-auto grid h-9 w-9 place-items-center rounded-full bg-emerald-400/15 text-[10px] font-black text-emerald-300">ZP</div><p class="mt-1.5 text-[9px] font-semibold text-slate-300">Zeleno Polje</p></div>
                                    <span class="text-[10px] font-black text-slate-500">VS</span>
                                    <div class="text-center"><div class="mx-auto grid h-9 w-9 place-items-center rounded-full bg-white/10 text-[10px] font-black text-slate-300">FK</div><p class="mt-1.5 text-[9px] font-semibold text-slate-300">Rival</p></div>
                                </div>
                                <p class="mt-2.5 text-center text-[9px] font-semibold text-slate-400">Gradski stadion · Liga</p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between rounded-xl border border-white/[0.07] bg-white/[0.04] px-3.5 py-3">
                            <div><p class="text-[10px] font-bold text-white">Timovi spremni za vikend</p><p class="mt-1 text-[9px] text-slate-400">Utakmice, sastavi i zapisnici</p></div>
                            <div class="flex -space-x-2">
                                <span v-for="badge in ['U15', 'U17', 'U19']" :key="badge" class="grid h-8 w-8 place-items-center rounded-full border-2 border-[#10231a] bg-emerald-400 text-[8px] font-black text-emerald-950">{{ badge }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-5 -left-4 hidden rounded-xl border border-emerald-900/10 bg-white px-4 py-3 shadow-xl dark:border-white/10 dark:bg-slate-900 sm:block">
                        <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">Klub organizovan</p>
                        <p class="mt-1 text-sm font-extrabold text-emerald-700 dark:text-emerald-400">Sve na jednom mestu <span class="ml-1">✓</span></p>
                    </div>
                </div>
            </div>
        </section>

        <section id="funkcionalnosti" class="scroll-mt-20 border-y border-slate-200/80 bg-white/70 py-16 dark:border-white/5 dark:bg-white/[0.02] sm:py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-400">Klub pod kontrolom</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Sve što vašem klubu treba</h2>
                    <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-400 sm:text-base">Od prvog treninga do završnog zvižduka — vodite svakodnevni rad kluba bez rasutih tabela i poruka.</p>
                </div>
                <div class="mt-11 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article v-for="feature in features" :key="feature.title" class="group rounded-2xl border border-slate-200 bg-[#fbfcfa] p-5 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-950/5 dark:border-white/[0.08] dark:bg-white/[0.03] dark:hover:border-emerald-800">
                        <div class="grid h-11 w-11 place-items-center rounded-xl bg-emerald-100 text-xl dark:bg-emerald-400/10">{{ feature.icon }}</div>
                        <h3 class="mt-4 text-base font-extrabold">{{ feature.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ feature.description }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="paketi" class="scroll-mt-20 py-16 sm:py-20">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center lg:px-10">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-400">Paketi za svaki klub</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Rastite svojim tempom.</h2>
                    <p class="mt-4 max-w-md text-sm leading-6 text-slate-600 dark:text-slate-400 sm:text-base">Izaberite paket prema potrebama kluba. Počnite sa osnovama, a dodatne mogućnosti su tu kada budete spremni.</p>
                    <router-link to="/register" class="mt-6 inline-flex items-center gap-2 text-sm font-extrabold text-emerald-700 hover:text-emerald-600 dark:text-emerald-400 dark:hover:text-emerald-300">
                        Registrujte svoj klub <span aria-hidden="true">→</span>
                    </router-link>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <article v-for="plan in plans" :key="plan.name" class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03]" :class="plan.featured ? 'ring-2 ring-emerald-400 shadow-xl shadow-emerald-950/10' : ''">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold">{{ plan.name }}</h3>
                            <span v-if="plan.featured" class="rounded-full bg-emerald-100 px-2 py-1 text-[9px] font-black uppercase tracking-wide text-emerald-800 dark:bg-emerald-400/15 dark:text-emerald-300">Najviše mogućnosti</span>
                        </div>
                        <p class="mt-4 text-3xl font-black">{{ plan.price }}<span class="ml-1 text-xs font-semibold text-slate-500">€/mes.</span></p>
                        <p class="mt-2 min-h-10 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ plan.description }}</p>
                        <div class="mt-4 h-px bg-slate-100 dark:bg-white/10"></div>
                        <p class="mt-4 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">{{ plan.includes }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="px-5 pb-16 sm:px-8 sm:pb-20 lg:px-10">
            <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 overflow-hidden rounded-[1.75rem] bg-[#0e2419] px-6 py-9 text-white sm:px-10 sm:py-11 md:flex-row md:items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-300">Sledeći korak počinje ovde</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight sm:text-3xl">Dajte svom klubu bolji pregled.</h2>
                    <p class="mt-2 text-sm text-slate-300">Registrujte klub i odaberite paket koji vam odgovara.</p>
                </div>
                <router-link to="/register" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-emerald-400 px-6 py-3 text-sm font-extrabold text-[#062216] transition hover:bg-emerald-300">
                    Započni registraciju
                </router-link>
            </div>
        </section>

        <footer class="border-t border-slate-200/80 px-5 py-6 dark:border-white/5 sm:px-8 lg:px-10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-xs text-slate-500 sm:flex-row dark:text-slate-400">
                <p><span class="font-extrabold text-slate-800 dark:text-slate-200">ProTrainer</span> · Alati za bolji rad fudbalskog kluba</p>
                <div class="flex items-center gap-4"><a href="#funkcionalnosti" class="hover:text-emerald-600 dark:hover:text-emerald-400">Funkcionalnosti</a><a href="#paketi" class="hover:text-emerald-600 dark:hover:text-emerald-400">Paketi</a><router-link to="/login" class="hover:text-emerald-600 dark:hover:text-emerald-400">Prijava</router-link></div>
            </div>
        </footer>
    </main>
</template>

<script setup>
const metrics = [
    { label: 'Aktivni igrači', value: '48', note: '3 tima' },
    { label: 'Treninzi ove nedelje', value: '06', note: '+ uredno isplanirano' },
    { label: 'Utakmice', value: '04', note: '1 sledeća' }
]

const features = [
    { icon: '👥', title: 'Timovi i igrači', description: 'Organizujte uzrasne kategorije, roster, profile igrača i stručni štab kluba.' },
    { icon: '📅', title: 'Treninzi i pozivnice', description: 'Zakažite treninge, pošaljite pozivnice i pratite RSVP odgovore igrača.' },
    { icon: '⚽', title: 'Utakmice i sastavi', description: 'Planirajte mečeve, predloge sastava i vodite zapisnik golova i asistencija.' },
    { icon: '📈', title: 'Statistika i analitika', description: 'Pratite učinak igrača, posećenost treninga i rezultate kroz sezonu.' },
    { icon: '💳', title: 'Klupske finansije', description: 'Evidentirajte uplate članarina i pratite pregled finansija po timu.' },
    { icon: '🧠', title: 'Taktika ekipe', description: 'Izaberite formaciju i rasporedite igrače na interaktivnom taktičkom terenu.' }
]

const plans = [
    { name: 'Basic', price: '10', description: 'Sve osnove za organizovan klub.', includes: 'Timovi · Igrači · Profil kluba', featured: false },
    { name: 'Standard', price: '20', description: 'Za klubove koji žele i utakmice.', includes: 'Sve iz Basic + Utakmice', featured: false },
    { name: 'Premium', price: '50', description: 'Napredni alati za ambiciozne timove.', includes: 'Sve iz Standard + Analitika · Taktika', featured: true }
]
</script>
