<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Javni interfejs za pregled kazni i banova na serveru'
    ],
    
    'nav' => [
        'home' => 'Početna',
        'bans' => 'Banovi',
        'mutes' => 'Utišavanja',
        'warnings' => 'Upozorenja',
        'kicks' => 'Izbacivanja',
        'statistics' => 'Statistika',
        'language' => 'Jezik',
        'theme' => 'Tema',
        'admin' => 'Admin',
        'protest' => 'Žalba na ban',
    ],
    
    'home' => [
        'welcome' => 'Kazne na serveru',
        'description' => 'Pretražite kazne igrača i pregledajte nedavne aktivnosti',
        'recent_activity' => 'Nedavne aktivnosti',
        'recent_bans' => 'Nedavni banovi',
        'recent_mutes' => 'Nedavna utišavanja',
        'no_recent_bans' => 'Nema nedavnih banova',
        'no_recent_mutes' => 'Nema nedavnih utišavanja',
        'view_all_bans' => 'Prikaži sve banove',
        'view_all_mutes' => 'Prikaži sva utišavanja'
    ],
    
    'search' => [
        'title' => 'Pretraga igrača',
        'placeholder' => 'Unesite ime igrača ili UUID...',
        'help' => 'Možete pretraživati po imenu igrača ili punom UUID-u',
        'button' => 'Pretraži',
        'no_results' => 'Nema pronađenih kazni za ovog igrača',
        'error' => 'Došlo je do greške prilikom pretrage',
        'network_error' => 'Došlo je do mrežne greške. Molimo pokušajte ponovo.'
    ],
    
    'stats' => [
        'title' => 'Statistika servera',
        'active_bans' => 'Aktivni banovi',
        'active_mutes' => 'Aktivna utišavanja',
        'total_warnings' => 'Ukupno upozorenja',
        'total_kicks' => 'Ukupno izbacivanja',
        'total_of' => 'od',
        'all_time' => 'ukupno',
        'most_banned_players' => 'Najčešće banovani igrači',
        'most_active_staff' => 'Najaktivniji članovi tima',
        'top_ban_reasons' => 'Najčešći razlozi za ban',
        'recent_activity_overview' => 'Pregled nedavnih aktivnosti',
        'activity_by_day' => 'Aktivnost po danima',
        'cache_cleared' => 'Keš statistike je uspešno obrisan',
        'cache_clear_failed' => 'Brisanje keša statistike nije uspelo',
        'clear_cache' => 'Obriši keš',
        'last_24h' => 'Poslednjih 24 sata',
        'last_7d' => 'Poslednjih 7 dana',
        'last_30d' => 'Poslednjih 30 dana'
    ],
    
    'table' => [
        'player' => 'Igrač',
        'reason' => 'Razlog',
        'staff' => 'Administrator',
        'date' => 'Datum',
        'expires' => 'Ističe',
        'status' => 'Status',
        'actions' => 'Akcije',
        'type' => 'Tip',
        'view' => 'Prikaži',
        'total' => 'Ukupno',
        'active' => 'Aktivan',
        'last_ban' => 'Poslednji ban',
        'last_action' => 'Poslednja akcija',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Aktivan',
        'inactive' => 'Neaktivan',
        'expired' => 'Istekao',
        'removed' => 'Uklonjen',
        'completed' => 'Završen',
        'removed_by' => 'Uklonio'
    ],
    
    'punishment' => [
        'permanent' => 'Trajno',
        'expired' => 'Istekao'
    ],
    
    'punishments' => [
        'no_data' => 'Nema pronađenih kazni',
        'no_data_desc' => 'Trenutno nema kazni za prikaz'
    ],
    
    'detail' => [
        'duration' => 'Trajanje',
        'time_left' => 'Preostalo vreme',
        'progress' => 'Napredak',
        'removed_by' => 'Uklonio',
        'removed_date' => 'Datum uklanjanja',
        'flags' => 'Oznake',
        'other_punishments' => 'Ostale kazne'
    ],
    
    'time' => [
        'days' => '{count} dana',
        'hours' => '{count} sati',
        'minutes' => '{count} minuta'
    ],
    
    'pagination' => [
        'label' => 'Navigacija stranice',
        'previous' => 'Prethodna',
        'next' => 'Sledeća',
        'page_info' => 'Stranica {current} od {total}'
    ],
    
    'footer' => [
        'rights' => 'Sva prava zadržana.',
        'powered_by' => 'Pokreće',
        'license' => 'Licencirano pod'
    ],
    
    'error' => [
        'not_found' => 'Stranica nije pronađena',
        'server_error' => 'Došlo je do greške na serveru',
        'invalid_request' => 'Nevažeći zahtev',
        'punishment_not_found' => 'Tražena kazna nije mogla biti pronađena.',
        'loading_failed' => 'Učitavanje detalja kazne nije uspelo.'
    ],
    
    'protest' => [
        'title' => 'Žalba na ban',
        'description' => 'Ako smatrate da je vaš ban izdat greškom, možete podneti žalbu na razmatranje.',
        'how_to_title' => 'Kako podneti žalbu na ban',
        'how_to_subtitle' => 'Pratite ove korake da biste zatražili ukidanje bana:',
        'step1_title' => '1. Prikupite informacije',
        'step1_desc' => 'Pre podnošenja žalbe, proverite da li imate:',
        'step1_items' => [
            'Vaše Minecraft korisničko ime',
            'Datum i vreme vašeg bana',
            'Razlog naveden za vaš ban',
            'Bilo kakav dokaz koji podržava vaš slučaj'
        ],
        'step2_title' => '2. Metode kontakta',
        'step2_desc' => 'Možete podneti žalbu na ban putem jedne od sledećih metoda:',
        'discord_title' => 'Discord (Preporučeno)',
        'discord_desc' => 'Pridružite se našem Discord serveru i kreirajte tiket u kanalu #zalbe-na-ban',
        'discord_button' => 'Pridruži se Discordu',
        'email_title' => 'Email',
        'email_desc' => 'Pošaljite detaljan email sa vašom žalbom na:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Kreirajte novu objavu u odeljku Žalbe na ban na našem forumu.',
        'forum_button' => 'Poseti forum',
        'step3_title' => '3. Šta uključiti',
        'step3_desc' => 'Vaša žalba treba da sadrži: Vaš nadimak - Šta se desilo - Zašto se žalite - Šta želite da se desi - ID sa sajta (npr. ban&id=181) - Opciono: Snimak ekrana kao dokaz.',
        'step3_items' => [
            'Vaše Minecraft korisničko ime',
            'Datum i približno vreme bana',
            'Član tima koji vas je banovao (ako je poznato)',
            'Detaljno objašnjenje zašto smatrate da je ban bio nepravedan',
            'Bilo kakve snimke ekrana ili dokaze koji podržavaju vaš slučaj',
            'Iskren opis onoga što se dogodilo'
        ],
        'step4_title' => '4. Sačekajte na pregled',
        'step4_desc' => 'Naš tim će pregledati vašu žalbu u roku od 48-72 sata. Molimo budite strpljivi i ne podnosite više žalbi za isti ban.',
        'guidelines_title' => 'Važne smernice',
        'guidelines_items' => [
            'Budite iskreni i poštujte druge u svojoj žalbi',
            'Ne lažite i ne pružajte lažne informacije',
            'Ne spamujte i ne podnosite više žalbi',
            'Prihvatite konačnu odluku tima',
            'Izbegavanje bana će rezultirati trajnim banom'
        ],
        'warning_title' => 'Upozorenje',
        'warning_desc' => 'Podnošenje lažnih informacija ili pokušaj obmane tima će rezultirati odbijanjem vaše žalbe i može dovesti do produženja kazne.',
        'form_not_available' => 'Direktno podnošenje žalbi trenutno nije dostupno. Molimo koristite jednu od gore navedenih metoda kontakta.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin panel',
        'login' => 'Prijava za admine',
        'logout' => 'Odjava',
        'password' => 'Lozinka',
        'export_data' => 'Izvoz podataka',
        'export_desc' => 'Izvoz podataka o kaznama u različitim formatima',
        'import_data' => 'Uvoz podataka',
        'import_desc' => 'Uvoz podataka o kaznama iz JSON ili XML fajlova',
        'data_type' => 'Tip podataka',
        'all_punishments' => 'Sve kazne',
        'select_file' => 'Izaberi fajl',
        'import' => 'Uvezi',
        'settings' => 'Podešavanja',
        'show_player_uuid' => 'Prikaži UUID igrača',
        'footer_site_name' => 'Naziv sajta u podnožju',
        'footer_site_name_desc' => 'Naziv sajta prikazan u tekstu o autorskim pravima u podnožju',
    ],
];