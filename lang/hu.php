<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Nyilvános felület a szerver büntetéseinek és kitiltásainak megtekintéséhez'
    ],
    
    'nav' => [
        'home' => 'Főoldal',
        'bans' => 'Kitiltások',
        'mutes' => 'Némítások',
        'warnings' => 'Figyelmeztetések',
        'kicks' => 'Kirúgások',
        'statistics' => 'Statisztika',
        'language' => 'Nyelv',
        'theme' => 'Téma',
        'admin' => 'Admin',
        'protest' => 'Kitiltás Fellebbezése',
    ],
    
    'home' => [
        'welcome' => 'Szerver Büntetések',
        'description' => 'Keress játékos büntetéseket és nézd meg a legutóbbi aktivitást',
        'recent_activity' => 'Legutóbbi Aktivitás',
        'recent_bans' => 'Legutóbbi Kitiltások',
        'recent_mutes' => 'Legutóbbi Némítások',
        'no_recent_bans' => 'Nincsenek friss kitiltások',
        'no_recent_mutes' => 'Nincsenek friss némítások',
        'view_all_bans' => 'Összes kitiltás megtekintése',
        'view_all_mutes' => 'Összes némítás megtekintése'
    ],
    
    'search' => [
        'title' => 'Játékos Keresése',
        'placeholder' => 'Add meg a játékos nevét vagy UUID-jét...',
        'help' => 'Kereshetsz játékosnév vagy teljes UUID alapján',
        'button' => 'Keresés',
        'no_results' => 'Nincs találat erre a játékosra',
        'error' => 'Hiba történt a keresés során',
        'network_error' => 'Hálózati hiba történt. Kérjük, próbálja újra.'
    ],
    
    'stats' => [
        'title' => 'Szerver Statisztika',
        'active_bans' => 'Aktív Kitiltások',
        'active_mutes' => 'Aktív Némítások',
        'total_warnings' => 'Összes Figyelmeztetés',
        'total_kicks' => 'Összes Kirúgás',
        'total_of' => '/',
        'all_time' => 'összesen',
        'most_banned_players' => 'Legtöbbször kitiltott játékosok',
        'most_active_staff' => 'Legaktívabb adminok',
        'top_ban_reasons' => 'Leggyakoribb kitiltási okok',
        'recent_activity_overview' => 'Legutóbbi aktivitás áttekintése',
        'activity_by_day' => 'Aktivitás naponként',
        'cache_cleared' => 'A statisztikai gyorsítótár sikeresen törölve',
        'cache_clear_failed' => 'Nem sikerült törölni a statisztikai gyorsítótárat',
        'clear_cache' => 'Gyorsítótár törlése',
        'last_24h' => 'Elmúlt 24 óra',
        'last_7d' => 'Elmúlt 7 nap',
        'last_30d' => 'Elmúlt 30 nap'
    ],
    
    'table' => [
        'player' => 'Játékos',
        'reason' => 'Ok',
        'staff' => 'Admin',
        'date' => 'Dátum',
        'expires' => 'Lejárat',
        'status' => 'Állapot',
        'actions' => 'Műveletek',
        'type' => 'Típus',
        'view' => 'Megtekintés',
        'total' => 'Összesen',
        'active' => 'Aktív',
        'last_ban' => 'Utolsó kitiltás',
        'last_action' => 'Utolsó művelet',
        'server' => 'Szerver',
    ],
    
    'status' => [
        'active' => 'Aktív',
        'inactive' => 'Inaktív',
        'expired' => 'Lejárt',
        'removed' => 'Eltávolítva',
        'completed' => 'Befejezve',
        'removed_by' => 'Eltávolította'
    ],
    
    'punishment' => [
        'permanent' => 'Végleges',
        'expired' => 'Lejárt'
    ],
    
    'punishments' => [
        'no_data' => 'Nincsenek büntetések',
        'no_data_desc' => 'Jelenleg nincsenek megjeleníthető büntetések'
    ],
    
    'detail' => [
        'duration' => 'Időtartam',
        'time_left' => 'Hátralévő idő',
        'progress' => 'Folyamat',
        'removed_by' => 'Eltávolította',
        'removed_date' => 'Eltávolítás dátuma',
        'flags' => 'Jelzők',
        'other_punishments' => 'Egyéb büntetések'
    ],
    
    'time' => [
        'days' => '{count} nap',
        'hours' => '{count} óra',
        'minutes' => '{count} perc'
    ],
    
    'pagination' => [
        'label' => 'Oldalnavigáció',
        'previous' => 'Előző',
        'next' => 'Következő',
        'page_info' => '{current}. oldal / {total}'
    ],
    
    'footer' => [
        'rights' => 'Minden jog fenntartva.',
        'powered_by' => 'Működteti:',
        'license' => 'Licenc:'
    ],
    
    'error' => [
        'not_found' => 'Az oldal nem található',
        'server_error' => 'Szerverhiba történt',
        'invalid_request' => 'Érvénytelen kérés',
        'punishment_not_found' => 'A kért büntetés nem található.',
        'loading_failed' => 'A büntetés részleteinek betöltése sikertelen.'
    ],
    
    'protest' => [
        'title' => 'Kitiltás Fellebbezése',
        'description' => 'Ha úgy gondolod, hogy a kitiltásodat tévedésből kaptad, fellebbezést nyújthatsz be felülvizsgálatra.',
        'how_to_title' => 'Hogyan nyújts be fellebbezést',
        'how_to_subtitle' => 'Kövesd ezeket a lépéseket a kitiltás feloldásához:',
        'step1_title' => '1. Gyűjtsd össze az információkat',
        'step1_desc' => 'Mielőtt fellebbezést nyújtasz be, győződj meg róla, hogy rendelkezel a következőkkel:',
        'step1_items' => [
            'A Minecraft felhasználóneved',
            'A kitiltásod dátuma és ideje',
            'A kitiltásod oka',
            'Bármilyen bizonyíték, ami alátámasztja az ügyedet'
        ],
        'step2_title' => '2. Kapcsolatfelvételi módok',
        'step2_desc' => 'A fellebbezésedet az alábbi módok egyikén nyújthatod be:',
        'discord_title' => 'Discord (Ajánlott)',
        'discord_desc' => 'Csatlakozz a Discord szerverünkhöz, és hozz létre egy jegyet a #kitiltas-fellebbezes csatornán',
        'discord_button' => 'Csatlakozás a Discordhoz',
        'email_title' => 'E-mail',
        'email_desc' => 'Küldj egy részletes e-mailt a fellebbezéseddel a következő címre:',
        'forum_title' => 'Fórum',
        'forum_desc' => 'Hozzon létre egy új bejegyzést a weboldalunk fórumának Kitiltási fellebbezések szakaszában.',
        'forum_button' => 'Fórum meglátogatása',
        'step3_title' => '3. Mit tartalmazzon',
        'step3_desc' => 'A fellebbezésednek tartalmaznia kell: A beceneved - Mi történt - Miért fellebbezel - Mit szeretnél, hogy történjen - Azonosító az oldalról (pl. ban&id=181) - Opcionális: Képernyőkép bizonyítékként.',
        'step3_items' => [
            'A Minecraft felhasználóneved',
            'A kitiltás dátuma és hozzávetőleges ideje',
            'Az admin, aki kitiltott (ha tudod)',
            'Részletes magyarázat arról, miért gondolod, hogy a kitiltás igazságtalan volt',
            'Bármilyen képernyőkép vagy bizonyíték, ami alátámasztja az ügyedet',
            'Őszinte beszámoló arról, ami történt'
        ],
        'step4_title' => '4. Várj a felülvizsgálatra',
        'step4_desc' => 'Csapatunk 48-72 órán belül felülvizsgálja a fellebbezésedet. Kérjük, légy türelmes, és ne nyújts be több fellebbezést ugyanarra a kitiltásra.',
        'guidelines_title' => 'Fontos irányelvek',
        'guidelines_items' => [
            'Légy őszinte és tisztelettudó a fellebbezésedben',
            'Ne hazudj és ne adj meg hamis információkat',
            'Ne spammelj és ne nyújts be több fellebbezést',
            'Fogadd el a csapat végső döntését',
            'A kitiltás kijátszása végleges kitiltást von maga után'
        ],
        'warning_title' => 'Figyelmeztetés',
        'warning_desc' => 'Hamis információk megadása vagy a csapat megtévesztésének kísérlete a fellebbezésed elutasítását vonja maga után, és meghosszabbított büntetéshez vezethet.',
        'form_not_available' => 'A közvetlen fellebbezés benyújtása jelenleg nem elérhető. Kérjük, használd a fenti kapcsolatfelvételi módok egyikét.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin Felület',
        'login' => 'Admin Bejelentkezés',
        'logout' => 'Kijelentkezés',
        'password' => 'Jelszó',
        'export_data' => 'Adatok Exportálása',
        'export_desc' => 'Büntetési adatok exportálása különböző formátumokban',
        'import_data' => 'Adatok Importálása',
        'import_desc' => 'Büntetési adatok importálása JSON vagy XML fájlokból',
        'data_type' => 'Adattípus',
        'all_punishments' => 'Minden büntetés',
        'select_file' => 'Fájl kiválasztása',
        'import' => 'Importálás',
        'settings' => 'Beállítások',
        'show_player_uuid' => 'Játékos UUID megjelenítése',
        'footer_site_name' => 'Oldal neve a láblécben',
        'footer_site_name_desc' => 'A lábléc szerzői jogi szövegében megjelenő oldal neve',
    ],
];