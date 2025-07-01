<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Veřejné rozhraní pro zobrazování trestů a banů na serveru'
    ],
    
    'nav' => [
        'home' => 'Domů',
        'bans' => 'Bany',
        'mutes' => 'Umlčení',
        'warnings' => 'Varování',
        'kicks' => 'Vyhození',
        'statistics' => 'Statistiky',
        'language' => 'Jazyk',
        'theme' => 'Vzhled',
        'admin' => 'Admin',
        'protest' => 'Žádost o unban',
    ],
    
    'home' => [
        'welcome' => 'Tresty na serveru',
        'description' => 'Vyhledejte tresty hráčů a zobrazte si nedávnou aktivitu',
        'recent_activity' => 'Nedávná aktivita',
        'recent_bans' => 'Nedávné bany',
        'recent_mutes' => 'Nedávná umlčení',
        'no_recent_bans' => 'Nebyly nalezeny žádné nedávné bany',
        'no_recent_mutes' => 'Nebyly nalezeny žádná nedávná umlčení',
        'view_all_bans' => 'Zobrazit všechny bany',
        'view_all_mutes' => 'Zobrazit všechna umlčení'
    ],
    
    'search' => [
        'title' => 'Vyhledávání hráčů',
        'placeholder' => 'Zadejte jméno hráče nebo UUID...',
        'help' => 'Můžete vyhledávat podle jména hráče nebo celého UUID',
        'button' => 'Hledat',
        'no_results' => 'Pro tohoto hráče nebyly nalezeny žádné tresty',
        'error' => 'Při vyhledávání došlo k chybě',
        'network_error' => 'Došlo k chybě sítě. Zkuste to prosím znovu.'
    ],
    
    'stats' => [
        'title' => 'Statistiky serveru',
        'active_bans' => 'Aktivní bany',
        'active_mutes' => 'Aktivní umlčení',
        'total_warnings' => 'Celkový počet varování',
        'total_kicks' => 'Celkový počet vyhození',
        'total_of' => 'z',
        'all_time' => 'celkově',
        'most_banned_players' => 'Nejčastěji banovaní hráči',
        'most_active_staff' => 'Nejaktivnější členové týmu',
        'top_ban_reasons' => 'Nejčastější důvody banů',
        'recent_activity_overview' => 'Přehled nedávné aktivity',
        'activity_by_day' => 'Aktivita podle dnů',
        'cache_cleared' => 'Cache statistik byla úspěšně vymazána',
        'cache_clear_failed' => 'Nepodařilo se vymazat cache statistik',
        'clear_cache' => 'Vymazat cache',
        'last_24h' => 'Posledních 24 hodin',
        'last_7d' => 'Posledních 7 dní',
        'last_30d' => 'Posledních 30 dní'
    ],
    
    'table' => [
        'player' => 'Hráč',
        'reason' => 'Důvod',
        'staff' => 'Admin',
        'date' => 'Datum',
        'expires' => 'Vyprší',
        'status' => 'Stav',
        'actions' => 'Akce',
        'type' => 'Typ',
        'view' => 'Zobrazit',
        'total' => 'Celkem',
        'active' => 'Aktivní',
        'last_ban' => 'Poslední ban',
        'last_action' => 'Poslední akce',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Aktivní',
        'inactive' => 'Neaktivní',
        'expired' => 'Vypršel',
        'removed' => 'Odstraněn',
        'completed' => 'Dokončen',
        'removed_by' => 'Odstranil'
    ],
    
    'punishment' => [
        'permanent' => 'Trvalý',
        'expired' => 'Vypršel'
    ],
    
    'punishments' => [
        'no_data' => 'Nebyly nalezeny žádné tresty',
        'no_data_desc' => 'Momentálně nejsou k zobrazení žádné tresty'
    ],
    
    'detail' => [
        'duration' => 'Doba trvání',
        'time_left' => 'Zbývající čas',
        'progress' => 'Průběh',
        'removed_by' => 'Odstranil',
        'removed_date' => 'Datum odstranění',
        'flags' => 'Příznaky',
        'other_punishments' => 'Ostatní tresty'
    ],
    
    'time' => [
        'days' => '{count} dní',
        'hours' => '{count} hodin',
        'minutes' => '{count} minut'
    ],
    
    'pagination' => [
        'label' => 'Navigace stránky',
        'previous' => 'Předchozí',
        'next' => 'Další',
        'page_info' => 'Stránka {current} z {total}'
    ],
    
    'footer' => [
        'rights' => 'Všechna práva vyhrazena.',
        'powered_by' => 'Běží na',
        'license' => 'Licencováno pod'
    ],
    
    'error' => [
        'not_found' => 'Stránka nenalezena',
        'server_error' => 'Došlo k chybě serveru',
        'invalid_request' => 'Neplatný požadavek',
        'punishment_not_found' => 'Požadovaný trest nebyl nalezen.',
        'loading_failed' => 'Nepodařilo se načíst podrobnosti o trestu.'
    ],
    
    'protest' => [
        'title' => 'Žádost o unban',
        'description' => 'Pokud se domníváte, že váš ban byl udělen omylem, můžete podat žádost o přezkoumání.',
        'how_to_title' => 'Jak podat žádost o unban',
        'how_to_subtitle' => 'Pro žádost o zrušení banu postupujte podle těchto kroků:',
        'step1_title' => '1. Shromážděte si informace',
        'step1_desc' => 'Před podáním žádosti se ujistěte, že máte:',
        'step1_items' => [
            'Vaše jméno v Minecraftu',
            'Datum a čas vašeho banu',
            'Uvedený důvod vašeho banu',
            'Jakékoliv důkazy, které podporují váš případ'
        ],
        'step2_title' => '2. Způsoby kontaktování',
        'step2_desc' => 'Svou žádost o unban můžete podat jedním z následujících způsobů:',
        'discord_title' => 'Discord (Doporučeno)',
        'discord_desc' => 'Připojte se na náš Discord server a vytvořte ticket v kanálu #žádosti-o-unban',
        'discord_button' => 'Připojit se na Discord',
        'email_title' => 'E-mail',
        'email_desc' => 'Pošlete podrobný e-mail se svou žádostí na:',
        'forum_title' => 'Fórum',
        'forum_desc' => 'Vytvořte nový příspěvek v sekci Žádosti o unban na našem webovém fóru.',
        'forum_button' => 'Navštívit fórum',
        'step3_title' => '3. Co uvést',
        'step3_desc' => 'Vaše žádost by měla obsahovat: Vaši přezdívku - Co se stalo - Proč žádáte o unban - Co chcete, aby se stalo - ID ze stránky (např. ban&id=181) - Volitelně: Screenshot jako důkaz.',
        'step3_items' => [
            'Vaše jméno v Minecraftu',
            'Datum a přibližný čas banu',
            'Člen týmu, který vám dal ban (pokud je znám)',
            'Podrobné vysvětlení, proč si myslíte, že ban byl nespravedlivý',
            'Jakékoliv screenshoty nebo důkazy, které podporují váš případ',
            'Upřímný popis toho, co se stalo'
        ],
        'step4_title' => '4. Počkejte na přezkoumání',
        'step4_desc' => 'Náš tým přezkoumá vaši žádost do 48-72 hodin. Buďte prosím trpěliví a neposílejte více žádostí pro ten samý ban.',
        'guidelines_title' => 'Důležité pokyny',
        'guidelines_items' => [
            'Ve své žádosti buďte upřímní a uctiví',
            'Nelžete a neposkytujte nepravdivé informace',
            'Nespamujte ani neposílejte více žádostí',
            'Přijměte konečné rozhodnutí týmu',
            'Obcházení banu bude mít za následek trvalý ban'
        ],
        'warning_title' => 'Varování',
        'warning_desc' => 'Poskytnutí nepravdivých informací nebo pokus o oklamání týmu bude mít za následek zamítnutí vaší žádosti a může vést k prodloužení trestu.',
        'form_not_available' => 'Přímé podání žádosti momentálně není k dispozici. Použijte prosím jeden z výše uvedených způsobů kontaktování.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin panel',
        'login' => 'Přihlášení admina',
        'logout' => 'Odhlásit se',
        'password' => 'Heslo',
        'export_data' => 'Exportovat data',
        'export_desc' => 'Exportovat data o trestech v různých formátech',
        'import_data' => 'Importovat data',
        'import_desc' => 'Importovat data o trestech ze souborů JSON nebo XML',
        'data_type' => 'Typ dat',
        'all_punishments' => 'Všechny tresty',
        'select_file' => 'Vybrat soubor',
        'import' => 'Importovat',
        'settings' => 'Nastavení',
        'show_player_uuid' => 'Zobrazit UUID hráče',
        'footer_site_name' => 'Název stránky v patičce',
        'footer_site_name_desc' => 'Název stránky zobrazený v textu autorských práv v patičce',
    ],
];