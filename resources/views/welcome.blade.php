<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacera — Gestion de pharmacie</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#0b0d10] text-neutral-300 antialiased">

    <div class="min-h-screen flex flex-col">

        {{-- Top bar --}}
        <header class="border-b border-white/10">
            <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="text-white font-medium tracking-tight">Pharmacera</span>
                </div>
                <a href="/admin/login"
                   class="text-sm text-neutral-400 hover:text-white transition-colors">
                    Se connecter
                </a>
            </div>
        </header>

        {{-- Main --}}
        <main class="flex-1">
            <div class="max-w-5xl mx-auto px-6 pt-24 pb-16">

                <p class="text-sm text-emerald-400/80 font-medium mb-4">
                    Outil interne de gestion
                </p>

                <h1 class="text-4xl sm:text-5xl font-semibold text-white tracking-tight max-w-2xl leading-[1.15]">
                    Le stock, les ventes et les rapports de votre pharmacie, au même endroit.
                </h1>

                <p class="mt-6 text-lg text-neutral-400 max-w-xl leading-relaxed">
                    Pharmacera remplace le carnet et le tableur : suivi des lots et des dates
                    de péremption, ventes au comptoir, et rapports quotidiens — accessible
                    uniquement à votre équipe.
                </p>

                <div class="mt-8">
                    <a href="/admin/login"
                       class="inline-flex items-center gap-2 bg-white text-black text-sm font-medium
                              px-5 py-3 rounded-lg hover:bg-neutral-200 transition-colors">
                        Accéder à l'espace de gestion
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                {{-- Feature list — plain, not icon-in-circle cards --}}
                <div class="mt-20 grid sm:grid-cols-2 gap-x-12 gap-y-8 max-w-3xl border-t border-white/10 pt-12">

                    <div>
                        <h3 class="text-white text-sm font-medium mb-1.5">Inventaire par lot</h3>
                        <p class="text-sm text-neutral-500 leading-relaxed">
                            Chaque lot est suivi séparément avec sa date de péremption.
                            Alertes automatiques pour le stock faible et les produits proches de l'expiration.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-white text-sm font-medium mb-1.5">Ventes au comptoir</h3>
                        <p class="text-sm text-neutral-500 leading-relaxed">
                            Scan du code-barres ou sélection manuelle. Le stock est déduit
                            automatiquement du lot le plus proche de la péremption.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-white text-sm font-medium mb-1.5">Rapports PDF</h3>
                        <p class="text-sm text-neutral-500 leading-relaxed">
                            Chiffre d'affaires, ventes par employé, produits les plus vendus
                            et valeur du stock — exportables en un clic.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-white text-sm font-medium mb-1.5">Accès par rôle</h3>
                        <p class="text-sm text-neutral-500 leading-relaxed">
                            Le titulaire, le pharmacien et le caissier n'ont pas accès aux mêmes
                            informations. Chacun voit ce qui le concerne.
                        </p>
                    </div>

                </div>

            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-white/10">
            <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between text-xs text-neutral-600">
                <span>Pharmacera</span>
                <span>Usage interne uniquement</span>
            </div>
        </footer>

    </div>

</body>
</html>