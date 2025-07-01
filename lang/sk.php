<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Verejné rozhranie na prezeranie trestov a banov na serveri'
    ],
    
    'nav' => [
        'home' => 'Domov',
        'bans' => 'Bany',
        'mutes' => 'Umlčania',
        'warnings' => 'Varovania',
        'kicks' => 'Vyhodenia',
        'statistics' => 'Štatistiky',
        'language' => 'Jazyk',
        'theme' => 'Téma',
        'admin' => 'Admin',
        'protest' => 'Žiadosť o unban',
    ],
    
    'home' => [
        'welcome' => 'Tresty na serveri',
        'description' => 'Vyhľadajte tresty hráčov a pozrite si nedávnu aktivitu',
        'recent_activity' => 'Nedávna aktivita',
        'recent_bans' => 'Nedávne bany',
        'recent_mutes' => 'Nedávne umlčania',
        'no_recent_bans' => 'Nenašli sa žiadne nedávne bany',
        'no_recent_mutes' => 'Nenašli sa žiadne nedávne umlčania',
        'view_all_bans' => 'Zobraziť všetky bany',
        'view_all_mutes' => 'Zobraziť všetky umlčania'
    ],
    
    'search' => [
        'title' => 'Vyhľadávanie hráčov',
        'placeholder' => 'Zadajte meno hráča alebo UUID...',
        'help' => 'Môžete vyhľadávať podľa mena hráča alebo celého UUID',
        'button' => 'Hľadať',
        'no_results' => 'Pre tohto hráča sa nenašli žiadne tresty',
        'error' => 'Vyskytla sa chyba pri vyhľadávaní',
        'network_error' => 'Vyskytla sa chyba siete. Skúste to znova.'
    ],
    
    'stats' => [
        'title' => 'Štatistiky servera',
        'active_bans' => 'Aktívne bany',
        'active_mutes' => 'Aktívne umlčania',
        'total_warnings' => 'Celkový počet varovaní',
        'total_kicks' => 'Celkový počet vyhodení',
        'total_of' => 'z',
        'all_time' => 'celkovo',
        'most_banned_players' => 'Najviac banovaní hráči',
        'most_active_staff' => 'Najaktívnejší členovia tímu',
        'top_ban_reasons' => 'Najčastejšie dôvody banov',
        'recent_activity_overview' => 'Prehľad nedávnej aktivity',
        'activity_by_day' => 'Aktivita podľa dní',
        'cache_cleared' => 'Cache štatistík bola úspešne vymazaná',
        'cache_clear_failed' => 'Nepodarilo sa vymazať cache štatistík',
        'clear_cache' => 'Vymazať cache',
        'last_24h' => 'Posledných 24 hodín',
        'last_7d' => 'Posledných 7 dní',
        'last_30d' => 'Posledných 30 dní'
    ],
    
    'table' => [
        'player' => 'Hráč',
        'reason' => 'Dôvod',
        'staff' => 'Admin',
        'date' => 'Dátum',
        'expires' => 'Vyprší',
        'status' => 'Stav',
        'actions' => 'Akcie',
        'type' => 'Typ',
        'view' => 'Zobraziť',
        'total' => 'Celkom',
        'active' => 'Aktívny',
        'last_ban' => 'Posledný ban',
        'last_action' => 'Posledná akcia',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Aktívny',
        'inactive' => 'Neaktívny',
        'expired' => 'Vypršal',
        'removed' => 'Odstránený',
        'completed' => 'Ukončený',
        'removed_by' => 'Odstránil'
    ],
    
    'punishment' => [
        'permanent' => 'Trvalý',
        'expired' => 'Vypršal'
    ],
    
    'punishments' => [
        'no_data' => 'Nenašli sa žiadne tresty',
        'no_data_desc' => 'Momentálne nie sú na zobrazenie žiadne tresty'
    ],
    
    'detail' => [
        'duration' => 'Trvanie',
        'time_left' => 'Zostávajúci čas',
        'progress' => 'Priebeh',
        'removed_by' => 'Odstránil',
        'removed_date' => 'Dátum odstránenia',
        'flags' => 'Príznaky',
        'other_punishments' => 'Ostatné tresty'
    ],
    
    'time' => [
        'days' => '{count} dní',
        'hours' => '{count} hodín',
        'minutes' => '{count} minút'
    ],
    
    'pagination' => [
        'label' => 'Navigácia stránky',
        'previous' => 'Predchádzajúca',
        'next' => 'Nasledujúca',
        'page_info' => 'Stránka {current} z {total}'
    ],
    
    'footer' => [
        'rights' => 'Všetky práva vyhradené.',
        'powered_by' => 'Beží na',
        'license' => 'Licencované pod'
    ],
    
    'error' => [
        'not_found' => 'Stránka sa nenašla',
        'server_error' => 'Vyskytla sa chyba servera',
        'invalid_request' => 'Neplatná požiadavka',
        'punishment_not_found' => 'Požadovaný trest sa nepodarilo nájsť.',
        'loading_failed' => 'Nepodarilo sa načítať podrobnosti o treste.'
    ],
    
    'protest' => [
        'title' => 'Žiadosť o unban',
        'description' => 'Ak si myslíte, že váš ban bol udelený omylom, môžete podať žiadosť o preskúmanie.',
        'how_to_title' => 'Ako podať žiadosť o unban',
        'how_to_subtitle' => 'Pre žiadosť o zrušenie banu postupujte podľa týchto krokov:',
        'step1_title' => '1. Zozbierajte si informácie',
        'step1_desc' => 'Pred podaním žiadosti sa uistite, že máte:',
        'step1_items' => [
            'Vaše meno v Minecrafte',
            'Dátum a čas vášho banu',
            'Uvedený dôvod vášho banu',
            'Akékoľvek dôkazy, ktoré podporujú váš prípad'
        ],
        'step2_title' => '2. Spôsoby kontaktovania',
        'step2_desc' => 'Svoju žiadosť o unban môžete podať jedným z nasledujúcich spôsobov:',
        'discord_title' => 'Discord (Odporúčané)',
        'discord_desc' => 'Pripojte sa na náš Discord server a vytvorte ticket v kanáli #žiadosti-o-unban',
        'discord_button' => 'Pripojiť sa na Discord',
        'email_title' => 'E-mail',
        'email_desc' => 'Pošlite podrobný e-mail so svojou žiadosťou na:',
        'forum_title' => 'Fórum',
        'forum_desc' => 'Vytvorte nový príspevok v sekcii Žiadosti o unban na našom webovom fóre.',
        'forum_button' => 'Navštíviť fórum',
        'step3_title' => '3. Čo uviesť',
        'step3_desc' => 'Vaša žiadosť by mala obsahovať: Vašu prezývku - Čo sa stalo - Prečo žiadate o unban - Čo chcete, aby sa stalo - ID zo stránky (napr. ban&id=181) - Voliteľné: Screenshot ako dôkaz.',
        'step3_items' => [
            'Vaše meno v Minecrafte',
            'Dátum a približný čas banu',
            'Člen tímu, ktorý vám dal ban (ak je známy)',
            'Podrobné vysvetlenie, prečo si myslíte, že ban bol nespravodlivý',
            'Akékoľvek screenshoty alebo dôkazy, ktoré podporujú váš prípad',
            'Úprimný opis toho, čo sa stalo'
        ],
        'step4_title' => '4. Počkajte na preskúmanie',
        'step4_desc' => 'Náš tím preskúma vašu žiadosť do 48-72 hodín. Buďte prosím trpezliví a neposielajte viacero žiadostí pre ten istý ban.',
        'guidelines_title' => 'Dôležité pokyny',
        'guidelines_items' => [
            'Vo svojej žiadosti buďte úprimní a rešpektujúci',
            'Neklamte a neposkytujte nepravdivé informácie',
            'Nesnažte sa spamovať ani neposielajte viacero žiadostí',
            'Akceptujte konečné rozhodnutie tímu',
            'Obchádzanie banu bude mať za následok trvalý ban'
        ],
        'warning_title' => 'Varovanie',
        'warning_desc' => 'Poskytnutie nepravdivých informácií alebo pokus o oklamanie tímu bude mať za následok zamietnutie vašej žiadosti a môže viesť k predĺženiu trestu.',
        'form_not_available' => 'Priame podanie žiadosti momentálne nie je k dispozícii. Použite prosím jeden z vyššie uvedených spôsobov kontaktovania.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin panel',
        'login' => 'Prihlásenie admina',
        'logout' => 'Odhlásiť sa',
        'password' => 'Heslo',
        'export_data' => 'Exportovať dáta',
        'export_desc' => 'Exportovať dáta o trestoch v rôznych formátoch',
        'import_data' => 'Importovať dáta',
        'import_desc' => 'Importovať dáta o trestoch zo súborov JSON alebo XML',
        'data_type' => 'Typ dát',
        'all_punishments' => 'Všetky tresty',
        'select_file' => 'Vybrať súbor',
        'import' => 'Importovať',
        'settings' => 'Nastavenia',
        'show_player_uuid' => 'Zobraziť UUID hráča',
        'footer_site_name' => 'Názov stránky v pätičke',
'footer_site_name_desc' => 'Názov stránky zobrazený v texte autorských práv v pätičke',
    ],
];

