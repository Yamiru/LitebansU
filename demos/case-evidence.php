<?php
/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name:   LiteBansU
 * Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Version:       5.0
 * Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 * Author URI:    https://yamiru.com
 * License:       MIT
 * License URI:   https://opensource.org/licenses/MIT
 * Repository:    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const SN_DIR = __DIR__ . '/data';
const SN_FILE = SN_DIR . '/staff_notes.php';
const SN_LEGACY = SN_DIR . '/staff_notes.json';
const SN_GUARD = "<?php http_response_code(404); exit; ?>\n";
const SN_EVIDENCE = SN_DIR . '/evidence';
const SN_TYPES = ['ban', 'mute', 'warning', 'kick'];
const SN_APPEAL = ['none', 'pending', 'accepted', 'rejected'];
const SN_IMAGES = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const SN_VIDEOS = [
    'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mkv' => 'video/x-matroska', 'mov' => 'video/quicktime',
    'avi' => 'video/x-msvideo', 'm4v' => 'video/x-m4v', 'wmv' => 'video/x-ms-wmv', 'flv' => 'video/x-flv',
    'mpg' => 'video/mpeg', 'mpeg' => 'video/mpeg', '3gp' => 'video/3gpp', 'm4a' => 'audio/mp4',
    'dem' => 'application/octet-stream', 'demo' => 'application/octet-stream',
];
const SN_ACCEPT = 'image/png,image/jpeg,image/gif,image/webp,video/*,.mkv,.dem,.demo,.m4a';
// Videos in these formats are re-encoded to a smaller MP4 in the background (needs ffmpeg on the server)
const SN_OPTIMIZE = ['mp4', 'mkv', 'mov', 'avi', 'webm', 'flv', 'wmv', 'mpg', 'mpeg', 'm4v', '3gp'];
const SN_MAX_BYTES = 8 * 1024 * 1024;
const SN_MAX_VIDEO = 500 * 1024 * 1024;
const SN_MAX_UPLOAD = 6;
const SN_MAX_PER_NOTE = 12;
const SN_MAX_TEXT = 5000;
const SN_MAX_CAPTION = 120;
const SN_MAX_APPEAL = 160;

function sn_e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** UI strings. Missing keys fall back to English. Placeholders: %d numbers, %s text. */
function sn_strings(): array
{
    static $all = null;
    if ($all !== null) {
        return $all;
    }
    $en = [
        'title' => "Staff notes and evidence",
        'private' => "Staff only. Players never see this.",
        'empty' => "No staff report yet. Write what happened and why this punishment was issued, so another moderator can handle an appeal without asking.",
        'report' => "Staff report",
        'report_ph' => "What the player did and why it justified this punishment.",
        'add' => "Add staff report",
        'save' => "Save changes",
        'edit' => "Edit or delete",
        'del' => "Delete report",
        'remove' => "Remove",
        'confirm_img' => "Remove this image?",
        'confirm_note' => "Delete this report and its images?",
        'edited' => "edited",
        'readonly' => "Your role can read staff reports but not add them.",
        'by' => "by",
        'ap_title' => "Appeal",
        'ap_none' => "No appeal",
        'ap_pending' => "Pending",
        'ap_accepted' => "Accepted",
        'ap_rejected' => "Rejected",
        'ap_line_ph' => "One line on how the appeal was resolved",
        'ap_save' => "Save appeal",
        'ok_added' => "Staff report added.",
        'ok_updated' => "Staff report updated.",
        'ok_deleted' => "Staff report deleted.",
        'ok_img' => "Image removed.",
        'ok_appeal' => "Appeal updated.",
        'e_generic' => "Something went wrong. Reload the page and try again.",
        'e_text' => "Write what happened (up to %d characters).",
        'e_perm' => "You do not have permission to do this.",
        'e_note' => "That note no longer exists.",
        'e_own' => "You can only change your own notes.",
        'e_many' => "Too many images. You can add %d more.",
        'e_save' => "Could not save. Check that demos/data is writable.",
        'a_reports' => "Staff reports",
        'a_pending' => "Appeals pending",
        'a_latest' => "Latest reports",
        'a_punish' => "Punishment",
        'a_author' => "Author",
        'a_date' => "Date",
        'a_all' => "All",
        'a_empty' => "No staff reports yet. Open any ban and add the first one.",
        'tab' => "Case Evidence",
        'shots' => "Images and videos",
        'shots_hint' => "Images up to 8 MB. Videos and demos (MP4, WebM, MKV, MOV, DEM) up to 500 MB.",
        'caption_ph' => "Describe this file",
        'e_img' => "%s is not an accepted file or is too large.",
        'a_att' => "Attachments",
        'c_type' => "Type",
        'c_add' => "Add to case",
        'c_open' => "Open case",
        'c_sum' => "%d reports, %d files",
        'c_id' => "Case ID",
        'c_none' => "No evidence yet.",
        't_ban' => "Ban",
        't_mute' => "Mute",
        't_warning' => "Warning",
        't_kick' => "Kick",
        'refresh' => "Refresh",
        'e_toobig' => "The upload is larger than the server allows (%s). Use smaller files, or raise post_max_size and upload_max_filesize in the PHP settings.",
        'public_label' => "Show this report and its files on the public punishment page",
        'public_badge' => "Public",
        'public_title' => "Evidence",
    ];

    $tr = [];
    $tr['sk'] = [
        'title' => "Poznámky a dôkazy personálu",
        'private' => "Iba pre personál. Hráči to nikdy nevidia.",
        'empty' => "Zatiaľ žiadny report. Napíšte, čo sa stalo a prečo bol trest udelený, aby iný moderátor vedel vybaviť odvolanie bez pýtania.",
        'report' => "Report personálu", 'report_ph' => "Čo hráč urobil a prečo to odôvodňuje tento trest.",
        'add' => "Pridať report", 'save' => "Uložiť zmeny",
        'edit' => "Upraviť alebo zmazať", 'del' => "Zmazať report", 'remove' => "Odstrániť",
        'confirm_img' => "Odstrániť tento obrázok?", 'confirm_note' => "Zmazať tento report a jeho obrázky?",
        'edited' => "upravené", 'readonly' => "Vaša rola môže reporty čítať, ale nie pridávať.",
        'by' => "od",
        'ap_title' => "Odvolanie", 'ap_none' => "Bez odvolania", 'ap_pending' => "Čaká sa",
        'ap_accepted' => "Prijaté", 'ap_rejected' => "Zamietnuté", 'ap_line_ph' => "Jeden riadok o tom, ako sa odvolanie vyriešilo",
        'ap_save' => "Uložiť odvolanie",
        'ok_added' => "Report bol pridaný.", 'ok_updated' => "Report bol upravený.", 'ok_deleted' => "Report bol zmazaný.",
        'ok_img' => "Obrázok bol odstránený.", 'ok_appeal' => "Odvolanie bolo aktualizované.",
        'e_generic' => "Niečo sa pokazilo. Načítajte stránku znova a skúste to ešte raz.",
        'e_text' => "Napíšte, čo sa stalo (najviac %d znakov).", 'e_perm' => "Na toto nemáte oprávnenie.",
        'e_note' => "Táto poznámka už neexistuje.", 'e_own' => "Môžete meniť iba vlastné poznámky.",
        'e_many' => "Príliš veľa obrázkov. Môžete pridať ešte %d.",
        'e_save' => "Uloženie zlyhalo. Skontrolujte, či je demos/data zapisovateľný.",
        'a_reports' => "Reporty personálu", 'a_pending' => "Čakajúce odvolania",
        'a_latest' => "Najnovšie reporty", 'a_punish' => "Trest", 'a_author' => "Autor", 'a_date' => "Dátum", 'a_all' => "Všetky",
        'a_empty' => "Zatiaľ žiadne reporty. Otvorte ľubovoľný ban a pridajte prvý.",
        'tab' => "Dôkazy k prípadu", 'shots' => "Obrázky a videá", 'shots_hint' => "Obrázky do 8 MB. Videá a dema (MP4, WebM, MKV, MOV, DEM) do 500 MB.", 'caption_ph' => "Popíšte tento súbor", 'e_img' => "%s nie je povolený súbor alebo je príliš veľký.", 'a_att' => "Prílohy", 'c_type' => "Typ", 'c_add' => "Pridať k prípadu", 'c_open' => "Otvoriť prípad", 'c_sum' => "%d reportov, %d súborov", 'c_id' => "ID prípadu", 'c_none' => "Zatiaľ žiadne dôkazy.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Varovanie", 't_kick' => "Vyhodenie",
        'refresh' => "Obnoviť",
        'e_toobig' => "Nahrávanie je väčšie, než server povoľuje (%s). Použite menšie súbory alebo zvýšte post_max_size a upload_max_filesize v nastaveniach PHP.",
        'public_label' => "Zobraziť tento report a jeho súbory na verejnej stránke trestu",
        'public_badge' => "Verejné",
        'public_title' => "Dôkazy",
    ];
    $tr['cs'] = [
        'title' => "Poznámky a důkazy personálu",
        'private' => "Pouze pro personál. Hráči to nikdy nevidí.",
        'empty' => "Zatím žádný report. Napište, co se stalo a proč byl trest udělen, aby jiný moderátor vyřídil odvolání bez ptaní.",
        'report' => "Report personálu", 'report_ph' => "Co hráč udělal a proč to odůvodňuje tento trest.",
        'add' => "Přidat report", 'save' => "Uložit změny",
        'edit' => "Upravit nebo smazat", 'del' => "Smazat report", 'remove' => "Odebrat",
        'confirm_img' => "Odebrat tento obrázek?", 'confirm_note' => "Smazat tento report a jeho obrázky?",
        'edited' => "upraveno", 'readonly' => "Vaše role může reporty číst, ale ne přidávat.",
        'by' => "od",
        'ap_title' => "Odvolání", 'ap_none' => "Bez odvolání", 'ap_pending' => "Čeká se",
        'ap_accepted' => "Přijato", 'ap_rejected' => "Zamítnuto", 'ap_line_ph' => "Jeden řádek o tom, jak bylo odvolání vyřešeno",
        'ap_save' => "Uložit odvolání",
        'ok_added' => "Report byl přidán.", 'ok_updated' => "Report byl upraven.", 'ok_deleted' => "Report byl smazán.",
        'ok_img' => "Obrázek byl odebrán.", 'ok_appeal' => "Odvolání bylo aktualizováno.",
        'e_generic' => "Něco se pokazilo. Načtěte stránku znovu a zkuste to ještě jednou.",
        'e_text' => "Napište, co se stalo (nejvýše %d znaků).", 'e_perm' => "K tomu nemáte oprávnění.",
        'e_note' => "Tato poznámka už neexistuje.", 'e_own' => "Měnit můžete jen vlastní poznámky.",
        'e_many' => "Příliš mnoho obrázků. Můžete přidat ještě %d.",
        'e_save' => "Uložení selhalo. Zkontrolujte, zda je demos/data zapisovatelný.",
        'a_reports' => "Reporty personálu", 'a_pending' => "Čekající odvolání",
        'a_latest' => "Nejnovější reporty", 'a_punish' => "Trest", 'a_author' => "Autor", 'a_date' => "Datum", 'a_all' => "Všechny",
        'a_empty' => "Zatím žádné reporty. Otevřete libovolný ban a přidejte první.",
        'tab' => "Důkazy k případu", 'shots' => "Obrázky a videa", 'shots_hint' => "Obrázky do 8 MB. Videa a dema (MP4, WebM, MKV, MOV, DEM) do 500 MB.", 'caption_ph' => "Popište tento soubor", 'e_img' => "%s není povolený soubor nebo je příliš velký.", 'a_att' => "Přílohy", 'c_type' => "Typ", 'c_add' => "Přidat k případu", 'c_open' => "Otevřít případ", 'c_sum' => "%d reportů, %d souborů", 'c_id' => "ID případu", 'c_none' => "Zatím žádné důkazy.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Varování", 't_kick' => "Vyhození",
        'refresh' => "Obnovit",
        'e_toobig' => "Nahrávání je větší, než server povoluje (%s). Použijte menší soubory nebo zvyšte post_max_size a upload_max_filesize v nastavení PHP.",
        'public_label' => "Zobrazit tento report a jeho soubory na veřejné stránce trestu",
        'public_badge' => "Veřejné",
        'public_title' => "Důkazy",
    ];
    $tr['de'] = [
        'title' => "Team-Notizen und Beweise",
        'private' => "Nur für das Team. Spieler sehen das nie.",
        'empty' => "Noch kein Bericht. Schreibe, was passiert ist und warum die Strafe verhängt wurde, damit jemand anderes einen Einspruch ohne Rückfrage bearbeiten kann.",
        'report' => "Team-Bericht", 'report_ph' => "Was der Spieler getan hat und warum es diese Strafe rechtfertigt.",
        'add' => "Bericht hinzufügen", 'save' => "Änderungen speichern",
        'edit' => "Bearbeiten oder löschen", 'del' => "Bericht löschen", 'remove' => "Entfernen",
        'confirm_img' => "Dieses Bild entfernen?", 'confirm_note' => "Diesen Bericht und seine Bilder löschen?",
        'edited' => "bearbeitet", 'readonly' => "Deine Rolle darf Berichte lesen, aber nicht hinzufügen.",
        'by' => "von",
        'ap_title' => "Einspruch", 'ap_none' => "Kein Einspruch", 'ap_pending' => "Offen",
        'ap_accepted' => "Angenommen", 'ap_rejected' => "Abgelehnt", 'ap_line_ph' => "Eine Zeile dazu, wie der Einspruch entschieden wurde",
        'ap_save' => "Einspruch speichern",
        'ok_added' => "Bericht hinzugefügt.", 'ok_updated' => "Bericht aktualisiert.", 'ok_deleted' => "Bericht gelöscht.",
        'ok_img' => "Bild entfernt.", 'ok_appeal' => "Einspruch aktualisiert.",
        'e_generic' => "Etwas ist schiefgelaufen. Lade die Seite neu und versuche es erneut.",
        'e_text' => "Schreibe, was passiert ist (höchstens %d Zeichen).", 'e_perm' => "Dafür fehlt dir die Berechtigung.",
        'e_note' => "Diese Notiz existiert nicht mehr.", 'e_own' => "Du kannst nur deine eigenen Notizen ändern.",
        'e_many' => "Zu viele Bilder. Du kannst noch %d hinzufügen.",
        'e_save' => "Speichern fehlgeschlagen. Prüfe, ob demos/data beschreibbar ist.",
        'a_reports' => "Team-Berichte", 'a_pending' => "Offene Einsprüche",
        'a_latest' => "Neueste Berichte", 'a_punish' => "Strafe", 'a_author' => "Autor", 'a_date' => "Datum", 'a_all' => "Alle",
        'a_empty' => "Noch keine Berichte. Öffne einen beliebigen Bann und füge den ersten hinzu.",
        'tab' => "Fallbeweise", 'shots' => "Bilder und Videos", 'shots_hint' => "Bilder bis 8 MB. Videos und Demos (MP4, WebM, MKV, MOV, DEM) bis 500 MB.", 'caption_ph' => "Diese Datei beschreiben", 'e_img' => "%s ist keine erlaubte Datei oder zu groß.", 'a_att' => "Anhänge", 'c_type' => "Typ", 'c_add' => "Zum Fall hinzufügen", 'c_open' => "Fall öffnen", 'c_sum' => "%d Berichte, %d Dateien", 'c_id' => "Fall-ID", 'c_none' => "Noch keine Beweise.", 't_ban' => "Bann", 't_mute' => "Mute", 't_warning' => "Verwarnung", 't_kick' => "Kick",
        'refresh' => "Aktualisieren",
        'e_toobig' => "Der Upload ist größer, als der Server erlaubt (%s). Verwende kleinere Dateien oder erhöhe post_max_size und upload_max_filesize in den PHP-Einstellungen.",
        'public_label' => "Diesen Bericht und seine Dateien auf der öffentlichen Strafseite anzeigen",
        'public_badge' => "Öffentlich",
        'public_title' => "Beweise",
    ];
    $tr['pl'] = [
        'title' => "Notatki i dowody zespołu",
        'private' => "Tylko dla zespołu. Gracze tego nie widzą.",
        'empty' => "Brak raportu. Opisz, co się stało i dlaczego nałożono karę, aby inny moderator mógł rozpatrzyć odwołanie bez dopytywania.",
        'report' => "Raport zespołu", 'report_ph' => "Co zrobił gracz i dlaczego uzasadnia to tę karę.",
        'add' => "Dodaj raport", 'save' => "Zapisz zmiany",
        'edit' => "Edytuj lub usuń", 'del' => "Usuń raport", 'remove' => "Usuń",
        'confirm_img' => "Usunąć ten obraz?", 'confirm_note' => "Usunąć ten raport i jego obrazy?",
        'edited' => "edytowano", 'readonly' => "Twoja rola może czytać raporty, ale nie dodawać.",
        'by' => "przez",
        'ap_title' => "Odwołanie", 'ap_none' => "Brak odwołania", 'ap_pending' => "Oczekuje",
        'ap_accepted' => "Przyjęte", 'ap_rejected' => "Odrzucone", 'ap_line_ph' => "Jedna linijka o tym, jak rozpatrzono odwołanie",
        'ap_save' => "Zapisz odwołanie",
        'ok_added' => "Raport dodany.", 'ok_updated' => "Raport zaktualizowany.", 'ok_deleted' => "Raport usunięty.",
        'ok_img' => "Obraz usunięty.", 'ok_appeal' => "Odwołanie zaktualizowane.",
        'e_generic' => "Coś poszło nie tak. Odśwież stronę i spróbuj ponownie.",
        'e_text' => "Opisz, co się stało (maksymalnie %d znaków).", 'e_perm' => "Nie masz uprawnień do tej czynności.",
        'e_note' => "Ta notatka już nie istnieje.", 'e_own' => "Możesz zmieniać tylko własne notatki.",
        'e_many' => "Za dużo obrazów. Możesz dodać jeszcze %d.",
        'e_save' => "Nie udało się zapisać. Sprawdź, czy demos/data jest zapisywalny.",
        'a_reports' => "Raporty zespołu", 'a_pending' => "Oczekujące odwołania",
        'a_latest' => "Najnowsze raporty", 'a_punish' => "Kara", 'a_author' => "Autor", 'a_date' => "Data", 'a_all' => "Wszystkie",
        'a_empty' => "Brak raportów. Otwórz dowolny ban i dodaj pierwszy.",
        'tab' => "Dowody w sprawie", 'shots' => "Obrazy i wideo", 'shots_hint' => "Obrazy do 8 MB. Wideo i dema (MP4, WebM, MKV, MOV, DEM) do 500 MB.", 'caption_ph' => "Opisz ten plik", 'e_img' => "%s nie jest dozwolonym plikiem albo jest za duży.", 'a_att' => "Załączniki", 'c_type' => "Typ", 'c_add' => "Dodaj do sprawy", 'c_open' => "Otwórz sprawę", 'c_sum' => "%d raportów, %d plików", 'c_id' => "ID sprawy", 'c_none' => "Brak dowodów.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Ostrzeżenie", 't_kick' => "Kick",
        'refresh' => "Odśwież",
        'e_toobig' => "Przesyłany plik jest większy, niż pozwala serwer (%s). Użyj mniejszych plików albo zwiększ post_max_size i upload_max_filesize w ustawieniach PHP.",
        'public_label' => "Pokaż ten raport i jego pliki na publicznej stronie kary",
        'public_badge' => "Publiczne",
        'public_title' => "Dowody",
    ];
    $tr['hu'] = [
        'title' => "Staff jegyzetek és bizonyítékok",
        'private' => "Csak a staffnak. A játékosok ezt sosem látják.",
        'empty' => "Még nincs jelentés. Írd le, mi történt és miért szabták ki a büntetést, hogy egy másik moderátor kérdés nélkül elbírálhassa a fellebbezést.",
        'report' => "Staff jelentés", 'report_ph' => "Mit tett a játékos, és miért indokolja ez a büntetést.",
        'add' => "Jelentés hozzáadása", 'save' => "Módosítások mentése",
        'edit' => "Szerkesztés vagy törlés", 'del' => "Jelentés törlése", 'remove' => "Eltávolítás",
        'confirm_img' => "Eltávolítod ezt a képet?", 'confirm_note' => "Törlöd ezt a jelentést és a képeit?",
        'edited' => "szerkesztve", 'readonly' => "A szereped olvashatja a jelentéseket, de nem adhat hozzá újat.",
        'by' => "által",
        'ap_title' => "Fellebbezés", 'ap_none' => "Nincs fellebbezés", 'ap_pending' => "Függőben",
        'ap_accepted' => "Elfogadva", 'ap_rejected' => "Elutasítva", 'ap_line_ph' => "Egy sor arról, hogyan döntöttek a fellebbezésről",
        'ap_save' => "Fellebbezés mentése",
        'ok_added' => "Jelentés hozzáadva.", 'ok_updated' => "Jelentés frissítve.", 'ok_deleted' => "Jelentés törölve.",
        'ok_img' => "Kép eltávolítva.", 'ok_appeal' => "Fellebbezés frissítve.",
        'e_generic' => "Valami elromlott. Töltsd újra az oldalt, és próbáld újra.",
        'e_text' => "Írd le, mi történt (legfeljebb %d karakter).", 'e_perm' => "Ehhez nincs jogosultságod.",
        'e_note' => "Ez a jegyzet már nem létezik.", 'e_own' => "Csak a saját jegyzeteidet módosíthatod.",
        'e_many' => "Túl sok kép. Még %d adható hozzá.",
        'e_save' => "A mentés nem sikerült. Ellenőrizd, hogy a demos/data írható-e.",
        'a_reports' => "Staff jelentések", 'a_pending' => "Függő fellebbezések",
        'a_latest' => "Legújabb jelentések", 'a_punish' => "Büntetés", 'a_author' => "Szerző", 'a_date' => "Dátum", 'a_all' => "Mind",
        'a_empty' => "Még nincs jelentés. Nyiss meg egy bant, és add hozzá az elsőt.",
        'tab' => "Ügybizonyítékok", 'shots' => "Képek és videók", 'shots_hint' => "Képek legfeljebb 8 MB. Videók és demók (MP4, WebM, MKV, MOV, DEM) legfeljebb 500 MB.", 'caption_ph' => "Írd le ezt a fájlt", 'e_img' => "A(z) %s nem engedélyezett fájl, vagy túl nagy.", 'a_att' => "Mellékletek", 'c_type' => "Típus", 'c_add' => "Hozzáadás az ügyhöz", 'c_open' => "Ügy megnyitása", 'c_sum' => "%d jelentés, %d fájl", 'c_id' => "Ügy azonosítója", 'c_none' => "Még nincs bizonyíték.", 't_ban' => "Ban", 't_mute' => "Némítás", 't_warning' => "Figyelmeztetés", 't_kick' => "Kirúgás",
        'refresh' => "Frissítés",
        'e_toobig' => "A feltöltés nagyobb, mint amit a szerver engedélyez (%s). Használj kisebb fájlokat, vagy emeld meg a post_max_size és upload_max_filesize értékét a PHP beállításaiban.",
        'public_label' => "Jelentés és fájljai megjelenítése a nyilvános büntetésoldalon",
        'public_badge' => "Nyilvános",
        'public_title' => "Bizonyítékok",
    ];
    $tr['ro'] = [
        'title' => "Note și dovezi ale echipei",
        'private' => "Doar pentru echipă. Jucătorii nu văd niciodată asta.",
        'empty' => "Încă niciun raport. Scrie ce s-a întâmplat și de ce a fost dată sancțiunea, ca alt moderator să poată rezolva o contestație fără să întrebe.",
        'report' => "Raport al echipei", 'report_ph' => "Ce a făcut jucătorul și de ce justifică această sancțiune.",
        'add' => "Adaugă raport", 'save' => "Salvează modificările",
        'edit' => "Editează sau șterge", 'del' => "Șterge raportul", 'remove' => "Elimină",
        'confirm_img' => "Elimini această imagine?", 'confirm_note' => "Ștergi acest raport și imaginile lui?",
        'edited' => "editat", 'readonly' => "Rolul tău poate citi rapoarte, dar nu poate adăuga.",
        'by' => "de",
        'ap_title' => "Contestație", 'ap_none' => "Fără contestație", 'ap_pending' => "În așteptare",
        'ap_accepted' => "Acceptată", 'ap_rejected' => "Respinsă", 'ap_line_ph' => "O linie despre cum a fost rezolvată contestația",
        'ap_save' => "Salvează contestația",
        'ok_added' => "Raport adăugat.", 'ok_updated' => "Raport actualizat.", 'ok_deleted' => "Raport șters.",
        'ok_img' => "Imagine eliminată.", 'ok_appeal' => "Contestație actualizată.",
        'e_generic' => "Ceva nu a mers. Reîncarcă pagina și încearcă din nou.",
        'e_text' => "Scrie ce s-a întâmplat (cel mult %d caractere).", 'e_perm' => "Nu ai permisiunea pentru asta.",
        'e_note' => "Nota nu mai există.", 'e_own' => "Poți modifica doar notele tale.",
        'e_many' => "Prea multe imagini. Mai poți adăuga %d.",
        'e_save' => "Nu s-a putut salva. Verifică dacă demos/data poate fi scris.",
        'a_reports' => "Rapoarte ale echipei", 'a_pending' => "Contestații în așteptare",
        'a_latest' => "Cele mai noi rapoarte", 'a_punish' => "Sancțiune", 'a_author' => "Autor", 'a_date' => "Data", 'a_all' => "Toate",
        'a_empty' => "Încă niciun raport. Deschide orice ban și adaugă-l pe primul.",
        'tab' => "Dovezile cazului", 'shots' => "Imagini și videoclipuri", 'shots_hint' => "Imagini până la 8 MB. Videoclipuri și demo-uri (MP4, WebM, MKV, MOV, DEM) până la 500 MB.", 'caption_ph' => "Descrie acest fișier", 'e_img' => "%s nu este un fișier acceptat sau este prea mare.", 'a_att' => "Atașamente", 'c_type' => "Tip", 'c_add' => "Adaugă la caz", 'c_open' => "Deschide cazul", 'c_sum' => "%d rapoarte, %d fișiere", 'c_id' => "ID caz", 'c_none' => "Încă nu există dovezi.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Avertisment", 't_kick' => "Kick",
        'refresh' => "Reîmprospătează",
        'e_toobig' => "Încărcarea este mai mare decât permite serverul (%s). Folosește fișiere mai mici sau mărește post_max_size și upload_max_filesize în setările PHP.",
        'public_label' => "Afișează acest raport și fișierele lui pe pagina publică a sancțiunii",
        'public_badge' => "Public",
        'public_title' => "Dovezi",
    ];
    $tr['ru'] = [
        'title' => "Заметки и доказательства персонала",
        'private' => "Только для персонала. Игроки этого не видят.",
        'empty' => "Отчёта пока нет. Опишите, что произошло и почему выдано наказание, чтобы другой модератор мог рассмотреть апелляцию, ни о чём не спрашивая.",
        'report' => "Отчёт персонала", 'report_ph' => "Что сделал игрок и почему это оправдывает наказание.",
        'add' => "Добавить отчёт", 'save' => "Сохранить изменения",
        'edit' => "Изменить или удалить", 'del' => "Удалить отчёт", 'remove' => "Убрать",
        'confirm_img' => "Убрать это изображение?", 'confirm_note' => "Удалить этот отчёт и его изображения?",
        'edited' => "изменено", 'readonly' => "Ваша роль может читать отчёты, но не добавлять их.",
        'by' => "от",
        'ap_title' => "Апелляция", 'ap_none' => "Без апелляции", 'ap_pending' => "Ожидает",
        'ap_accepted' => "Принята", 'ap_rejected' => "Отклонена", 'ap_line_ph' => "Одна строка о том, как решили апелляцию",
        'ap_save' => "Сохранить апелляцию",
        'ok_added' => "Отчёт добавлен.", 'ok_updated' => "Отчёт обновлён.", 'ok_deleted' => "Отчёт удалён.",
        'ok_img' => "Изображение удалено.", 'ok_appeal' => "Апелляция обновлена.",
        'e_generic' => "Что-то пошло не так. Обновите страницу и повторите попытку.",
        'e_text' => "Опишите, что произошло (не более %d символов).", 'e_perm' => "У вас нет прав на это действие.",
        'e_note' => "Этой заметки больше нет.", 'e_own' => "Вы можете менять только свои заметки.",
        'e_many' => "Слишком много изображений. Можно добавить ещё %d.",
        'e_save' => "Не удалось сохранить. Проверьте, доступна ли запись в demos/data.",
        'a_reports' => "Отчёты персонала", 'a_pending' => "Апелляции в ожидании",
        'a_latest' => "Последние отчёты", 'a_punish' => "Наказание", 'a_author' => "Автор", 'a_date' => "Дата", 'a_all' => "Все",
        'a_empty' => "Отчётов пока нет. Откройте любой бан и добавьте первый.",
        'tab' => "Доказательства по делу", 'shots' => "Изображения и видео", 'shots_hint' => "Изображения до 8 МБ. Видео и демо (MP4, WebM, MKV, MOV, DEM) до 500 МБ.", 'caption_ph' => "Опишите этот файл", 'e_img' => "%s — недопустимый файл или слишком большой.", 'a_att' => "Вложения", 'c_type' => "Тип", 'c_add' => "Добавить к делу", 'c_open' => "Открыть дело", 'c_sum' => "Отчётов: %d, файлов: %d", 'c_id' => "ID дела", 'c_none' => "Доказательств пока нет.", 't_ban' => "Бан", 't_mute' => "Мут", 't_warning' => "Предупреждение", 't_kick' => "Кик",
        'refresh' => "Обновить",
        'e_toobig' => "Загрузка больше, чем разрешает сервер (%s). Используйте файлы поменьше или увеличьте post_max_size и upload_max_filesize в настройках PHP.",
        'public_label' => "Показывать этот отчёт и его файлы на публичной странице наказания",
        'public_badge' => "Публично",
        'public_title' => "Доказательства",
    ];
    $tr['es'] = [
        'title' => "Notas y pruebas del staff",
        'private' => "Solo para el staff. Los jugadores nunca ven esto.",
        'empty' => "Aún no hay informe. Escribe qué pasó y por qué se aplicó el castigo, para que otro moderador pueda resolver una apelación sin preguntar.",
        'report' => "Informe del staff", 'report_ph' => "Qué hizo el jugador y por qué justifica este castigo.",
        'add' => "Añadir informe", 'save' => "Guardar cambios",
        'edit' => "Editar o eliminar", 'del' => "Eliminar informe", 'remove' => "Quitar",
        'confirm_img' => "¿Quitar esta imagen?", 'confirm_note' => "¿Eliminar este informe y sus imágenes?",
        'edited' => "editado", 'readonly' => "Tu rol puede leer informes pero no añadirlos.",
        'by' => "por",
        'ap_title' => "Apelación", 'ap_none' => "Sin apelación", 'ap_pending' => "Pendiente",
        'ap_accepted' => "Aceptada", 'ap_rejected' => "Rechazada", 'ap_line_ph' => "Una línea sobre cómo se resolvió la apelación",
        'ap_save' => "Guardar apelación",
        'ok_added' => "Informe añadido.", 'ok_updated' => "Informe actualizado.", 'ok_deleted' => "Informe eliminado.",
        'ok_img' => "Imagen quitada.", 'ok_appeal' => "Apelación actualizada.",
        'e_generic' => "Algo salió mal. Recarga la página e inténtalo de nuevo.",
        'e_text' => "Escribe qué pasó (máximo %d caracteres).", 'e_perm' => "No tienes permiso para hacer esto.",
        'e_note' => "Esa nota ya no existe.", 'e_own' => "Solo puedes cambiar tus propias notas.",
        'e_many' => "Demasiadas imágenes. Puedes añadir %d más.",
        'e_save' => "No se pudo guardar. Comprueba que demos/data sea escribible.",
        'a_reports' => "Informes del staff", 'a_pending' => "Apelaciones pendientes",
        'a_latest' => "Últimos informes", 'a_punish' => "Castigo", 'a_author' => "Autor", 'a_date' => "Fecha", 'a_all' => "Todos",
        'a_empty' => "Aún no hay informes. Abre cualquier ban y añade el primero.",
        'tab' => "Pruebas del caso", 'shots' => "Imágenes y vídeos", 'shots_hint' => "Imágenes de hasta 8 MB. Vídeos y demos (MP4, WebM, MKV, MOV, DEM) de hasta 500 MB.", 'caption_ph' => "Describe este archivo", 'e_img' => "%s no es un archivo permitido o es demasiado grande.", 'a_att' => "Adjuntos", 'c_type' => "Tipo", 'c_add' => "Añadir al caso", 'c_open' => "Abrir caso", 'c_sum' => "%d informes, %d archivos", 'c_id' => "ID del caso", 'c_none' => "Aún no hay pruebas.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Advertencia", 't_kick' => "Expulsión",
        'refresh' => "Actualizar",
        'e_toobig' => "La subida supera lo que permite el servidor (%s). Usa archivos más pequeños o aumenta post_max_size y upload_max_filesize en la configuración de PHP.",
        'public_label' => "Mostrar este informe y sus archivos en la página pública del castigo",
        'public_badge' => "Público",
        'public_title' => "Pruebas",
    ];
    $tr['fr'] = [
        'title' => "Notes et preuves du staff",
        'private' => "Réservé au staff. Les joueurs ne voient jamais ceci.",
        'empty' => "Pas encore de rapport. Écrivez ce qui s'est passé et pourquoi la sanction a été prononcée, pour qu'un autre modérateur puisse traiter un appel sans poser de questions.",
        'report' => "Rapport du staff", 'report_ph' => "Ce que le joueur a fait et pourquoi cela justifie cette sanction.",
        'add' => "Ajouter le rapport", 'save' => "Enregistrer les modifications",
        'edit' => "Modifier ou supprimer", 'del' => "Supprimer le rapport", 'remove' => "Retirer",
        'confirm_img' => "Retirer cette image ?", 'confirm_note' => "Supprimer ce rapport et ses images ?",
        'edited' => "modifié", 'readonly' => "Votre rôle peut lire les rapports mais pas en ajouter.",
        'by' => "par",
        'ap_title' => "Appel", 'ap_none' => "Aucun appel", 'ap_pending' => "En attente",
        'ap_accepted' => "Accepté", 'ap_rejected' => "Rejeté", 'ap_line_ph' => "Une ligne sur la façon dont l'appel a été réglé",
        'ap_save' => "Enregistrer l'appel",
        'ok_added' => "Rapport ajouté.", 'ok_updated' => "Rapport mis à jour.", 'ok_deleted' => "Rapport supprimé.",
        'ok_img' => "Image retirée.", 'ok_appeal' => "Appel mis à jour.",
        'e_generic' => "Une erreur est survenue. Rechargez la page et réessayez.",
        'e_text' => "Écrivez ce qui s'est passé (%d caractères maximum).", 'e_perm' => "Vous n'avez pas la permission de faire cela.",
        'e_note' => "Cette note n'existe plus.", 'e_own' => "Vous ne pouvez modifier que vos propres notes.",
        'e_many' => "Trop d'images. Vous pouvez en ajouter %d de plus.",
        'e_save' => "Enregistrement impossible. Vérifiez que demos/data est accessible en écriture.",
        'a_reports' => "Rapports du staff", 'a_pending' => "Appels en attente",
        'a_latest' => "Derniers rapports", 'a_punish' => "Sanction", 'a_author' => "Auteur", 'a_date' => "Date", 'a_all' => "Tous",
        'a_empty' => "Aucun rapport pour l'instant. Ouvrez un ban et ajoutez le premier.",
        'tab' => "Preuves du dossier", 'shots' => "Images et vidéos", 'shots_hint' => "Images jusqu'à 8 Mo. Vidéos et démos (MP4, WebM, MKV, MOV, DEM) jusqu'à 500 Mo.", 'caption_ph' => "Décrivez ce fichier", 'e_img' => "%s n'est pas un fichier accepté ou est trop volumineux.", 'a_att' => "Pièces jointes", 'c_type' => "Type", 'c_add' => "Ajouter au dossier", 'c_open' => "Ouvrir le dossier", 'c_sum' => "%d rapports, %d fichiers", 'c_id' => "ID du dossier", 'c_none' => "Aucune preuve pour l'instant.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Avertissement", 't_kick' => "Expulsion",
        'refresh' => "Actualiser",
        'e_toobig' => "Le téléversement dépasse la limite du serveur (%s). Utilisez des fichiers plus petits ou augmentez post_max_size et upload_max_filesize dans la configuration PHP.",
        'public_label' => "Afficher ce rapport et ses fichiers sur la page publique de la sanction",
        'public_badge' => "Public",
        'public_title' => "Preuves",
    ];
    $tr['it'] = [
        'title' => "Note e prove dello staff",
        'private' => "Solo per lo staff. I giocatori non lo vedono mai.",
        'empty' => "Ancora nessun rapporto. Scrivi cosa è successo e perché è stata data la sanzione, così un altro moderatore potrà gestire un ricorso senza chiedere.",
        'report' => "Rapporto dello staff", 'report_ph' => "Cosa ha fatto il giocatore e perché giustifica questa sanzione.",
        'add' => "Aggiungi rapporto", 'save' => "Salva modifiche",
        'edit' => "Modifica o elimina", 'del' => "Elimina rapporto", 'remove' => "Rimuovi",
        'confirm_img' => "Rimuovere questa immagine?", 'confirm_note' => "Eliminare questo rapporto e le sue immagini?",
        'edited' => "modificato", 'readonly' => "Il tuo ruolo può leggere i rapporti ma non aggiungerli.",
        'by' => "da",
        'ap_title' => "Ricorso", 'ap_none' => "Nessun ricorso", 'ap_pending' => "In attesa",
        'ap_accepted' => "Accolto", 'ap_rejected' => "Respinto", 'ap_line_ph' => "Una riga su come è stato risolto il ricorso",
        'ap_save' => "Salva ricorso",
        'ok_added' => "Rapporto aggiunto.", 'ok_updated' => "Rapporto aggiornato.", 'ok_deleted' => "Rapporto eliminato.",
        'ok_img' => "Immagine rimossa.", 'ok_appeal' => "Ricorso aggiornato.",
        'e_generic' => "Qualcosa è andato storto. Ricarica la pagina e riprova.",
        'e_text' => "Scrivi cosa è successo (massimo %d caratteri).", 'e_perm' => "Non hai il permesso di farlo.",
        'e_note' => "Questa nota non esiste più.", 'e_own' => "Puoi modificare solo le tue note.",
        'e_many' => "Troppe immagini. Puoi aggiungerne ancora %d.",
        'e_save' => "Salvataggio non riuscito. Controlla che demos/data sia scrivibile.",
        'a_reports' => "Rapporti dello staff", 'a_pending' => "Ricorsi in attesa",
        'a_latest' => "Ultimi rapporti", 'a_punish' => "Sanzione", 'a_author' => "Autore", 'a_date' => "Data", 'a_all' => "Tutti",
        'a_empty' => "Ancora nessun rapporto. Apri un ban qualsiasi e aggiungi il primo.",
        'tab' => "Prove del caso", 'shots' => "Immagini e video", 'shots_hint' => "Immagini fino a 8 MB. Video e demo (MP4, WebM, MKV, MOV, DEM) fino a 500 MB.", 'caption_ph' => "Descrivi questo file", 'e_img' => "%s non è un file consentito o è troppo grande.", 'a_att' => "Allegati", 'c_type' => "Tipo", 'c_add' => "Aggiungi al caso", 'c_open' => "Apri caso", 'c_sum' => "%d rapporti, %d file", 'c_id' => "ID del caso", 'c_none' => "Ancora nessuna prova.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Avviso", 't_kick' => "Kick",
        'refresh' => "Aggiorna",
        'e_toobig' => "Il caricamento supera il limite del server (%s). Usa file più piccoli o aumenta post_max_size e upload_max_filesize nelle impostazioni PHP.",
        'public_label' => "Mostra questo rapporto e i suoi file nella pagina pubblica della sanzione",
        'public_badge' => "Pubblico",
        'public_title' => "Prove",
    ];
    $tr['tr'] = [
        'title' => "Yetkili notları ve kanıtlar",
        'private' => "Yalnızca yetkililer içindir. Oyuncular bunu asla görmez.",
        'empty' => "Henüz rapor yok. Ne olduğunu ve cezanın neden verildiğini yazın; böylece başka bir moderatör itirazı soru sormadan çözebilsin.",
        'report' => "Yetkili raporu", 'report_ph' => "Oyuncu ne yaptı ve bu ceza neden gerekçeli.",
        'add' => "Rapor ekle", 'save' => "Değişiklikleri kaydet",
        'edit' => "Düzenle veya sil", 'del' => "Raporu sil", 'remove' => "Kaldır",
        'confirm_img' => "Bu görsel kaldırılsın mı?", 'confirm_note' => "Bu rapor ve görselleri silinsin mi?",
        'edited' => "düzenlendi", 'readonly' => "Rolünüz raporları okuyabilir ama ekleyemez.",
        'by' => "tarafından",
        'ap_title' => "İtiraz", 'ap_none' => "İtiraz yok", 'ap_pending' => "Bekliyor",
        'ap_accepted' => "Kabul edildi", 'ap_rejected' => "Reddedildi", 'ap_line_ph' => "İtirazın nasıl çözüldüğüne dair tek satır",
        'ap_save' => "İtirazı kaydet",
        'ok_added' => "Rapor eklendi.", 'ok_updated' => "Rapor güncellendi.", 'ok_deleted' => "Rapor silindi.",
        'ok_img' => "Görsel kaldırıldı.", 'ok_appeal' => "İtiraz güncellendi.",
        'e_generic' => "Bir şeyler ters gitti. Sayfayı yenileyip tekrar deneyin.",
        'e_text' => "Ne olduğunu yazın (en fazla %d karakter).", 'e_perm' => "Bunu yapma yetkiniz yok.",
        'e_note' => "Bu not artık yok.", 'e_own' => "Yalnızca kendi notlarınızı değiştirebilirsiniz.",
        'e_many' => "Çok fazla görsel. %d tane daha ekleyebilirsiniz.",
        'e_save' => "Kaydedilemedi. demos/data klasörünün yazılabilir olduğunu kontrol edin.",
        'a_reports' => "Yetkili raporları", 'a_pending' => "Bekleyen itirazlar",
        'a_latest' => "Son raporlar", 'a_punish' => "Ceza", 'a_author' => "Yazar", 'a_date' => "Tarih", 'a_all' => "Tümü",
        'a_empty' => "Henüz rapor yok. Herhangi bir ban açın ve ilkini ekleyin.",
        'tab' => "Dava kanıtları", 'shots' => "Görseller ve videolar", 'shots_hint' => "Görseller en fazla 8 MB. Videolar ve demolar (MP4, WebM, MKV, MOV, DEM) en fazla 500 MB.", 'caption_ph' => "Bu dosyayı açıklayın", 'e_img' => "%s kabul edilen bir dosya değil veya çok büyük.", 'a_att' => "Ekler", 'c_type' => "Tür", 'c_add' => "Davaya ekle", 'c_open' => "Davayı aç", 'c_sum' => "%d rapor, %d dosya", 'c_id' => "Dava kimliği", 'c_none' => "Henüz kanıt yok.", 't_ban' => "Ban", 't_mute' => "Susturma", 't_warning' => "Uyarı", 't_kick' => "Atma",
        'refresh' => "Yenile",
        'e_toobig' => "Yükleme sunucunun izin verdiğinden büyük (%s). Daha küçük dosyalar kullanın veya PHP ayarlarında post_max_size ve upload_max_filesize değerlerini artırın.",
        'public_label' => "Bu raporu ve dosyalarını herkese açık ceza sayfasında göster",
        'public_badge' => "Herkese açık",
        'public_title' => "Kanıtlar",
    ];
    $tr['gr'] = [
        'title' => "Σημειώσεις και αποδείξεις προσωπικού",
        'private' => "Μόνο για το προσωπικό. Οι παίκτες δεν το βλέπουν ποτέ.",
        'empty' => "Δεν υπάρχει ακόμη αναφορά. Γράψτε τι έγινε και γιατί δόθηκε η ποινή, ώστε άλλος συντονιστής να χειριστεί μια ένσταση χωρίς να ρωτήσει.",
        'report' => "Αναφορά προσωπικού", 'report_ph' => "Τι έκανε ο παίκτης και γιατί δικαιολογεί αυτή την ποινή.",
        'add' => "Προσθήκη αναφοράς", 'save' => "Αποθήκευση αλλαγών",
        'edit' => "Επεξεργασία ή διαγραφή", 'del' => "Διαγραφή αναφοράς", 'remove' => "Αφαίρεση",
        'confirm_img' => "Να αφαιρεθεί αυτή η εικόνα;", 'confirm_note' => "Να διαγραφεί αυτή η αναφορά και οι εικόνες της;",
        'edited' => "επεξεργάστηκε", 'readonly' => "Ο ρόλος σας μπορεί να διαβάζει αναφορές αλλά όχι να προσθέτει.",
        'by' => "από",
        'ap_title' => "Ένσταση", 'ap_none' => "Χωρίς ένσταση", 'ap_pending' => "Σε εκκρεμότητα",
        'ap_accepted' => "Εγκρίθηκε", 'ap_rejected' => "Απορρίφθηκε", 'ap_line_ph' => "Μία γραμμή για το πώς επιλύθηκε η ένσταση",
        'ap_save' => "Αποθήκευση ένστασης",
        'ok_added' => "Η αναφορά προστέθηκε.", 'ok_updated' => "Η αναφορά ενημερώθηκε.", 'ok_deleted' => "Η αναφορά διαγράφηκε.",
        'ok_img' => "Η εικόνα αφαιρέθηκε.", 'ok_appeal' => "Η ένσταση ενημερώθηκε.",
        'e_generic' => "Κάτι πήγε στραβά. Ανανεώστε τη σελίδα και δοκιμάστε ξανά.",
        'e_text' => "Γράψτε τι έγινε (έως %d χαρακτήρες).", 'e_perm' => "Δεν έχετε δικαίωμα για αυτή την ενέργεια.",
        'e_note' => "Αυτή η σημείωση δεν υπάρχει πια.", 'e_own' => "Μπορείτε να αλλάξετε μόνο τις δικές σας σημειώσεις.",
        'e_many' => "Πάρα πολλές εικόνες. Μπορείτε να προσθέσετε ακόμη %d.",
        'e_save' => "Η αποθήκευση απέτυχε. Ελέγξτε ότι το demos/data είναι εγγράψιμο.",
        'a_reports' => "Αναφορές προσωπικού", 'a_pending' => "Ενστάσεις σε εκκρεμότητα",
        'a_latest' => "Πρόσφατες αναφορές", 'a_punish' => "Ποινή", 'a_author' => "Συντάκτης", 'a_date' => "Ημερομηνία", 'a_all' => "Όλες",
        'a_empty' => "Δεν υπάρχουν ακόμη αναφορές. Ανοίξτε ένα ban και προσθέστε την πρώτη.",
        'tab' => "Αποδείξεις υπόθεσης", 'shots' => "Εικόνες και βίντεο", 'shots_hint' => "Εικόνες έως 8 MB. Βίντεο και demo (MP4, WebM, MKV, MOV, DEM) έως 500 MB.", 'caption_ph' => "Περιγράψτε αυτό το αρχείο", 'e_img' => "Το %s δεν είναι αποδεκτό αρχείο ή είναι πολύ μεγάλο.", 'a_att' => "Συνημμένα", 'c_type' => "Τύπος", 'c_add' => "Προσθήκη στην υπόθεση", 'c_open' => "Άνοιγμα υπόθεσης", 'c_sum' => "%d αναφορές, %d αρχεία", 'c_id' => "ID υπόθεσης", 'c_none' => "Δεν υπάρχουν ακόμη αποδείξεις.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Προειδοποίηση", 't_kick' => "Kick",
        'refresh' => "Ανανέωση",
        'e_toobig' => "Η μεταφόρτωση είναι μεγαλύτερη από όσο επιτρέπει ο διακομιστής (%s). Χρησιμοποιήστε μικρότερα αρχεία ή αυξήστε τα post_max_size και upload_max_filesize στις ρυθμίσεις PHP.",
        'public_label' => "Εμφάνιση αυτής της αναφοράς και των αρχείων της στη δημόσια σελίδα της ποινής",
        'public_badge' => "Δημόσιο",
        'public_title' => "Αποδείξεις",
    ];
    $tr['sr'] = [
        'title' => "Beleške i dokazi osoblja",
        'private' => "Samo za osoblje. Igrači ovo nikada ne vide.",
        'empty' => "Još nema izveštaja. Napišite šta se desilo i zašto je kazna izrečena, da bi drugi moderator mogao da reši žalbu bez pitanja.",
        'report' => "Izveštaj osoblja", 'report_ph' => "Šta je igrač uradio i zašto to opravdava ovu kaznu.",
        'add' => "Dodaj izveštaj", 'save' => "Sačuvaj izmene",
        'edit' => "Izmeni ili obriši", 'del' => "Obriši izveštaj", 'remove' => "Ukloni",
        'confirm_img' => "Ukloniti ovu sliku?", 'confirm_note' => "Obrisati ovaj izveštaj i njegove slike?",
        'edited' => "izmenjeno", 'readonly' => "Vaša uloga može da čita izveštaje, ali ne i da ih dodaje.",
        'by' => "od",
        'ap_title' => "Žalba", 'ap_none' => "Nema žalbe", 'ap_pending' => "Na čekanju",
        'ap_accepted' => "Prihvaćena", 'ap_rejected' => "Odbijena", 'ap_line_ph' => "Jedan red o tome kako je žalba rešena",
        'ap_save' => "Sačuvaj žalbu",
        'ok_added' => "Izveštaj je dodat.", 'ok_updated' => "Izveštaj je ažuriran.", 'ok_deleted' => "Izveštaj je obrisan.",
        'ok_img' => "Slika je uklonjena.", 'ok_appeal' => "Žalba je ažurirana.",
        'e_generic' => "Nešto nije u redu. Osvežite stranicu i pokušajte ponovo.",
        'e_text' => "Napišite šta se desilo (najviše %d znakova).", 'e_perm' => "Nemate dozvolu za ovo.",
        'e_note' => "Ta beleška više ne postoji.", 'e_own' => "Možete menjati samo svoje beleške.",
        'e_many' => "Previše slika. Možete dodati još %d.",
        'e_save' => "Čuvanje nije uspelo. Proverite da li se u demos/data može pisati.",
        'a_reports' => "Izveštaji osoblja", 'a_pending' => "Žalbe na čekanju",
        'a_latest' => "Najnoviji izveštaji", 'a_punish' => "Kazna", 'a_author' => "Autor", 'a_date' => "Datum", 'a_all' => "Sve",
        'a_empty' => "Još nema izveštaja. Otvorite bilo koji ban i dodajte prvi.",
        'tab' => "Dokazi po slučaju", 'shots' => "Slike i video", 'shots_hint' => "Slike do 8 MB. Video i demo snimci (MP4, WebM, MKV, MOV, DEM) do 500 MB.", 'caption_ph' => "Opišite ovaj fajl", 'e_img' => "%s nije dozvoljen fajl ili je prevelik.", 'a_att' => "Prilozi", 'c_type' => "Tip", 'c_add' => "Dodaj slučaju", 'c_open' => "Otvori slučaj", 'c_sum' => "%d izveštaja, %d fajlova", 'c_id' => "ID slučaja", 'c_none' => "Još nema dokaza.", 't_ban' => "Ban", 't_mute' => "Mute", 't_warning' => "Upozorenje", 't_kick' => "Kick",
        'refresh' => "Osveži",
        'e_toobig' => "Otpremanje je veće nego što server dozvoljava (%s). Koristite manje fajlove ili povećajte post_max_size i upload_max_filesize u PHP podešavanjima.",
        'public_label' => "Prikaži ovaj izveštaj i njegove fajlove na javnoj stranici kazne",
        'public_badge' => "Javno",
        'public_title' => "Dokazi",
    ];
    $tr['ja'] = [
        'title' => "スタッフメモと証拠",
        'private' => "スタッフ専用です。プレイヤーには表示されません。",
        'empty' => "レポートはまだありません。何が起きたか、なぜ処罰したかを書いておくと、別のモデレーターが確認なしで異議申し立てに対応できます。",
        'report' => "スタッフレポート", 'report_ph' => "プレイヤーが何をしたか、なぜこの処罰が妥当か。",
        'add' => "レポートを追加", 'save' => "変更を保存",
        'edit' => "編集または削除", 'del' => "レポートを削除", 'remove' => "削除",
        'confirm_img' => "この画像を削除しますか？", 'confirm_note' => "このレポートと画像を削除しますか？",
        'edited' => "編集済み", 'readonly' => "あなたの権限ではレポートの閲覧のみ可能です。",
        'by' => "投稿者",
        'ap_title' => "異議申し立て", 'ap_none' => "異議なし", 'ap_pending' => "対応待ち",
        'ap_accepted' => "承認", 'ap_rejected' => "却下", 'ap_line_ph' => "異議申し立ての結果を1行で",
        'ap_save' => "異議申し立てを保存",
        'ok_added' => "レポートを追加しました。", 'ok_updated' => "レポートを更新しました。", 'ok_deleted' => "レポートを削除しました。",
        'ok_img' => "画像を削除しました。", 'ok_appeal' => "異議申し立てを更新しました。",
        'e_generic' => "問題が発生しました。ページを再読み込みしてもう一度お試しください。",
        'e_text' => "何が起きたかを書いてください（最大%d文字）。", 'e_perm' => "この操作の権限がありません。",
        'e_note' => "そのメモは既に存在しません。", 'e_own' => "変更できるのは自分のメモだけです。",
        'e_many' => "画像が多すぎます。あと%d枚まで追加できます。",
        'e_save' => "保存できませんでした。demos/data が書き込み可能か確認してください。",
        'a_reports' => "スタッフレポート", 'a_pending' => "対応待ちの異議",
        'a_latest' => "最新のレポート", 'a_punish' => "処罰", 'a_author' => "作成者", 'a_date' => "日付", 'a_all' => "すべて",
        'a_empty' => "レポートはまだありません。BANを開いて最初のレポートを追加してください。",
        'tab' => "ケース証拠", 'shots' => "画像と動画", 'shots_hint' => "画像は8 MBまで。動画とデモ（MP4、WebM、MKV、MOV、DEM）は500 MBまで。", 'caption_ph' => "このファイルの説明", 'e_img' => "%s は許可されていないファイルか、サイズが大きすぎます。", 'a_att' => "添付ファイル", 'c_type' => "種類", 'c_add' => "ケースに追加", 'c_open' => "ケースを開く", 'c_sum' => "レポート%d件、ファイル%d件", 'c_id' => "ケースID", 'c_none' => "証拠はまだありません。", 't_ban' => "BAN", 't_mute' => "ミュート", 't_warning' => "警告", 't_kick' => "キック",
        'refresh' => "更新",
        'e_toobig' => "アップロードがサーバーの上限（%s）を超えています。小さいファイルを使うか、PHP設定の post_max_size と upload_max_filesize を引き上げてください。",
        'public_label' => "このレポートとファイルを公開の処罰ページに表示する",
        'public_badge' => "公開",
        'public_title' => "証拠",
    ];
    $tr['cn'] = [
        'title' => "管理员备注与证据",
        'private' => "仅管理员可见，玩家永远看不到。",
        'empty' => "还没有报告。请写下发生了什么以及为何处罚，这样其他管理员无需询问就能处理申诉。",
        'report' => "管理员报告", 'report_ph' => "玩家做了什么，以及为何应受此处罚。",
        'add' => "添加报告", 'save' => "保存更改",
        'edit' => "编辑或删除", 'del' => "删除报告", 'remove' => "移除",
        'confirm_img' => "移除这张图片？", 'confirm_note' => "删除此报告及其图片？",
        'edited' => "已编辑", 'readonly' => "你的角色可以查看报告，但不能添加。",
        'by' => "作者",
        'ap_title' => "申诉", 'ap_none' => "无申诉", 'ap_pending' => "待处理",
        'ap_accepted' => "已通过", 'ap_rejected' => "已驳回", 'ap_line_ph' => "用一行说明申诉如何处理",
        'ap_save' => "保存申诉",
        'ok_added' => "报告已添加。", 'ok_updated' => "报告已更新。", 'ok_deleted' => "报告已删除。",
        'ok_img' => "图片已移除。", 'ok_appeal' => "申诉已更新。",
        'e_generic' => "出错了。请刷新页面后重试。",
        'e_text' => "请写下发生了什么（最多 %d 个字符）。", 'e_perm' => "你没有权限执行此操作。",
        'e_note' => "该备注已不存在。", 'e_own' => "你只能修改自己的备注。",
        'e_many' => "图片太多。还可以再添加 %d 张。",
        'e_save' => "保存失败。请检查 demos/data 是否可写。",
        'a_reports' => "管理员报告", 'a_pending' => "待处理申诉",
        'a_latest' => "最新报告", 'a_punish' => "处罚", 'a_author' => "作者", 'a_date' => "日期", 'a_all' => "全部",
        'a_empty' => "还没有报告。打开任意封禁并添加第一条。",
        'tab' => "案件证据", 'shots' => "图片和视频", 'shots_hint' => "图片最大 8 MB。视频和 Demo（MP4、WebM、MKV、MOV、DEM）最大 500 MB。", 'caption_ph' => "描述此文件", 'e_img' => "%s 不是允许的文件，或文件过大。", 'a_att' => "附件", 'c_type' => "类型", 'c_add' => "添加到案件", 'c_open' => "打开案件", 'c_sum' => "%d 份报告，%d 个文件", 'c_id' => "案件 ID", 'c_none' => "暂无证据。", 't_ban' => "封禁", 't_mute' => "禁言", 't_warning' => "警告", 't_kick' => "踢出",
        'refresh' => "刷新",
        'e_toobig' => "上传内容超过服务器允许的大小（%s）。请使用较小的文件，或在 PHP 设置中调高 post_max_size 和 upload_max_filesize。",
        'public_label' => "在公开的处罚页面显示此报告及其文件",
        'public_badge' => "公开",
        'public_title' => "证据",
    ];
    $tr['ar'] = [
        'title' => "ملاحظات وأدلة الطاقم",
        'private' => "للطاقم فقط. لا يراه اللاعبون أبدًا.",
        'empty' => "لا يوجد تقرير بعد. اكتب ما حدث ولماذا صدرت العقوبة، ليتمكن مشرف آخر من معالجة الاعتراض دون أن يسأل.",
        'report' => "تقرير الطاقم", 'report_ph' => "ماذا فعل اللاعب ولماذا يبرر ذلك هذه العقوبة.",
        'add' => "إضافة تقرير", 'save' => "حفظ التغييرات",
        'edit' => "تعديل أو حذف", 'del' => "حذف التقرير", 'remove' => "إزالة",
        'confirm_img' => "إزالة هذه الصورة؟", 'confirm_note' => "حذف هذا التقرير وصوره؟",
        'edited' => "تم التعديل", 'readonly' => "دورك يسمح بقراءة التقارير دون إضافتها.",
        'by' => "بواسطة",
        'ap_title' => "الاعتراض", 'ap_none' => "لا يوجد اعتراض", 'ap_pending' => "قيد الانتظار",
        'ap_accepted' => "مقبول", 'ap_rejected' => "مرفوض", 'ap_line_ph' => "سطر واحد عن كيفية حل الاعتراض",
        'ap_save' => "حفظ الاعتراض",
        'ok_added' => "تمت إضافة التقرير.", 'ok_updated' => "تم تحديث التقرير.", 'ok_deleted' => "تم حذف التقرير.",
        'ok_img' => "تمت إزالة الصورة.", 'ok_appeal' => "تم تحديث الاعتراض.",
        'e_generic' => "حدث خطأ ما. أعد تحميل الصفحة وحاول مرة أخرى.",
        'e_text' => "اكتب ما حدث (حتى %d حرفًا).", 'e_perm' => "ليست لديك صلاحية لهذا الإجراء.",
        'e_note' => "هذه الملاحظة لم تعد موجودة.", 'e_own' => "يمكنك تعديل ملاحظاتك فقط.",
        'e_many' => "عدد الصور كبير جدًا. يمكنك إضافة %d أخرى.",
        'e_save' => "تعذّر الحفظ. تحقق من أن demos/data قابل للكتابة.",
        'a_reports' => "تقارير الطاقم", 'a_pending' => "اعتراضات قيد الانتظار",
        'a_latest' => "أحدث التقارير", 'a_punish' => "العقوبة", 'a_author' => "الكاتب", 'a_date' => "التاريخ", 'a_all' => "الكل",
        'a_empty' => "لا توجد تقارير بعد. افتح أي حظر وأضف أول تقرير.",
        'tab' => "أدلة القضية", 'shots' => "الصور والفيديو", 'shots_hint' => "الصور حتى 8 ميغابايت. الفيديو والعروض (MP4 وWebM وMKV وMOV وDEM) حتى 500 ميغابايت.", 'caption_ph' => "صف هذا الملف", 'e_img' => "%s ليس ملفًا مسموحًا أو أنه كبير جدًا.", 'a_att' => "المرفقات", 'c_type' => "النوع", 'c_add' => "إضافة إلى القضية", 'c_open' => "فتح القضية", 'c_sum' => "%d تقارير، %d ملفات", 'c_id' => "رقم القضية", 'c_none' => "لا توجد أدلة بعد.", 't_ban' => "حظر", 't_mute' => "كتم", 't_warning' => "تحذير", 't_kick' => "طرد",
        'refresh' => "تحديث",
        'e_toobig' => "حجم الرفع أكبر مما يسمح به الخادم (%s). استخدم ملفات أصغر أو ارفع post_max_size وupload_max_filesize في إعدادات PHP.",
        'public_label' => "عرض هذا التقرير وملفاته في صفحة العقوبة العامة",
        'public_badge' => "عام",
        'public_title' => "الأدلة",
    ];

    return $all = ['en' => $en] + array_map(fn($t) => $t + $en, $tr);
}

/** Interface language: session, cookie, browser, then English. Mirrors the site's own detection. */
function sn_lang(): string
{
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }
    $supported = array_keys(sn_strings());
    $candidates = [$_SESSION['selected_lang'] ?? null, $_COOKIE['selected_lang'] ?? null,
        strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2))];
    foreach ($candidates as $c) {
        if ($c === 'zh') {
            $c = 'cn';
        }
        if ($c && in_array($c, $supported, true)) {
            return $lang = $c;
        }
    }
    return $lang = 'en';
}

function sn_t(string $key, ...$args): string
{
    $text = sn_strings()[sn_lang()][$key] ?? $key;
    return $args ? vsprintf($text, $args) : $text;
}

/** Logged-in staff member with role and permissions, or null. Unknown accounts are read-only. */
function sn_user(): ?array
{
    static $cached = false;
    if ($cached !== false) {
        return $cached;
    }
    $cached = null;
    if (empty($_SESSION['admin_authenticated'])) {
        return null;
    }

    $name = (string)($_SESSION['admin_user'] ?? 'Administrator');
    $id = (string)($_SESSION['admin_user_id'] ?? 'legacy');
    if ($id === 'legacy') {
        return $cached = ['id' => 'legacy', 'name' => $name, 'role' => 'admin', 'permissions' => ['all']];
    }

    $role = 'viewer';
    $permissions = ['view'];
    try {
        $authFile = __DIR__ . '/../core/AuthManager.php';
        if (!class_exists('core\\AuthManager', false) && is_file($authFile)) {
            require_once $authFile;
        }
        if (class_exists('core\\AuthManager', false)) {
            $user = (new \core\AuthManager($GLOBALS['config'] ?? []))->getUserById($id);
            if ($user && ($user['active'] ?? true) === false) {
                return null;
            }
            if ($user) {
                $name = (string)($user['name'] ?? $name);
                $role = (string)($user['role'] ?? 'viewer');
                $permissions = (array)($user['permissions'] ?? []);
            }
        }
    } catch (Throwable $e) {
        // Keep the read-only defaults.
    }
    return $cached = ['id' => $id, 'name' => $name, 'role' => $role, 'permissions' => $permissions];
}

/**
 * view: any staff. add (reports and appeal outcome): admin or "modify".
 * edit/delete: admin, or "modify" on your own note.
 */
function sn_can(?array $user, string $action, ?array $note = null): bool
{
    if (!$user) {
        return false;
    }
    if ($action === 'view') {
        return true;
    }
    $all = $user['role'] === 'admin' || in_array('all', $user['permissions'], true);
    $writer = $all || in_array('modify', $user['permissions'], true);
    if ($action === 'add') {
        return $writer;
    }
    return $all || ($writer && $note !== null && ($note['author_id'] ?? '') === $user['id']);
}

function sn_key(string $type, int $id): string
{
    return $type . '_' . $id;
}

function sn_normalize($data): array
{
    $data = is_array($data) ? $data : [];
    foreach (['notes', 'appeals'] as $part) {
        if (!isset($data[$part]) || !is_array($data[$part])) {
            $data[$part] = [];
        }
    }
    return $data;
}

/** Removes the guard line from the stored file, so the rest is plain JSON. */
function sn_unguard(string $raw): string
{
    return preg_replace('/^<\?php[^\n]*\?>\r?\n/', '', $raw) ?? $raw;
}

function sn_read(): array
{
    $path = is_file(SN_FILE) ? SN_FILE : SN_LEGACY;
    $raw = @file_get_contents($path);
    return sn_normalize($raw ? json_decode(sn_unguard($raw), true) : null);
}

/** Read-modify-write under an exclusive lock. Return false from $change to skip saving. */
function sn_mutate(callable $change)
{
    if (!is_dir(SN_EVIDENCE)) {
        @mkdir(SN_EVIDENCE, 0775, true);
    }
    // Move an older unguarded staff_notes.json to the guarded file
    if (!is_file(SN_FILE) && is_file(SN_LEGACY)) {
        @rename(SN_LEGACY, SN_FILE);
    }
    $fh = @fopen(SN_FILE, 'c+');
    if (!$fh) {
        return false;
    }
    flock($fh, LOCK_EX);
    $data = sn_normalize(json_decode(sn_unguard((string)stream_get_contents($fh)), true));
    $result = $change($data);
    if ($result !== false) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $result = false;
        } else {
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, SN_GUARD . $json);
            fflush($fh);
        }
    }
    flock($fh, LOCK_UN);
    fclose($fh);
    return $result;
}

function sn_note_count(string $type, int $id): int
{
    return count(sn_read()['notes'][sn_key($type, $id)] ?? []);
}

function sn_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/** Site base path without trailing slash, whether called from the site or from /demos/. */
function sn_base(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    if (basename($dir) === 'demos') {
        $dir = dirname($dir);
    }
    return rtrim($dir === '.' ? '' : $dir, '/');
}

function sn_flash(string $kind, string $message): void
{
    $_SESSION['sn_flash'] = [$kind, $message];
}

function sn_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $pow = $bytes > 0 ? min((int)floor(log($bytes, 1024)), 3) : 0;
    return round($bytes / (1024 ** $pow), 1) . ' ' . $units[$pow];
}

function sn_date($iso): string
{
    return $iso ? date('Y-m-d H:i', strtotime((string)$iso)) : '';
}

function sn_ext_kind(string $ext): ?string
{
    if (isset(SN_IMAGES[$ext])) {
        return 'image';
    }
    return isset(SN_VIDEOS[$ext]) ? 'file' : null;
}

function sn_mime(string $ext): string
{
    return SN_IMAGES[$ext] ?? SN_VIDEOS[$ext] ?? 'application/octet-stream';
}

/**
 * Validate and store uploaded images, videos and demos.
 * Returns [saved attachment entries, error message or null].
 */
function sn_store_evidence(int $room, array $captions): array
{
    $saved = [];
    $files = $_FILES['evidence'] ?? null;
    if (!$files || !is_array($files['name'])) {
        return [$saved, null];
    }
    if (!is_dir(SN_EVIDENCE)) {
        @mkdir(SN_EVIDENCE, 0775, true);
    }
    $limit = max(0, min(SN_MAX_UPLOAD, $room));
    $count = 0;
    foreach ($files['name'] as $i => $original) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (in_array($files['error'][$i], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return [$saved, sn_t('e_toobig', sn_bytes(sn_ini_bytes((string)ini_get('upload_max_filesize'))))];
        }
        if (++$count > $limit) {
            return [$saved, sn_t('e_many', $limit)];
        }
        $tmp = $files['tmp_name'][$i];
        $ext = strtolower(pathinfo((string)$original, PATHINFO_EXTENSION));
        $kind = sn_ext_kind($ext);
        $clean = mb_substr(preg_replace('/[^\w.\- ]/u', '_', basename((string)$original)), 0, 80);
        $max = $kind === 'image' ? SN_MAX_BYTES : SN_MAX_VIDEO;
        if ($files['error'][$i] !== UPLOAD_ERR_OK || !is_uploaded_file($tmp) || $kind === null || $files['size'][$i] > $max) {
            return [$saved, sn_t('e_img', $clean)];
        }
        if ($kind === 'image') {
            $info = @getimagesize($tmp);
            if (!$info || $info['mime'] !== SN_IMAGES[$ext]) {
                return [$saved, sn_t('e_img', $clean)];
            }
        }
        $id = bin2hex(random_bytes(8));
        $stored = $id . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        $target = SN_EVIDENCE . '/' . $stored;
        if (!move_uploaded_file($tmp, $target)) {
            return [$saved, sn_t('e_save')];
        }
        if ($kind === 'image' && $ext !== 'webp') {
            $webp = SN_EVIDENCE . '/' . $id . '.webp';
            if (sn_webp($target, $ext, $webp) && filesize($webp) < filesize($target)) {
                @unlink($target);
                $target = $webp;
                $stored = $id . '.webp';
                $clean = pathinfo($clean, PATHINFO_FILENAME) . '.webp';
            } else {
                @unlink($webp);
            }
        } elseif ($kind === 'file' && in_array($ext, SN_OPTIMIZE, true)) {
            sn_optimize_video($target, $id);
        }
        $saved[] = [
            'id' => $id,
            'file' => $stored,
            'kind' => $kind,
            'name' => $clean,
            'caption' => mb_substr(trim((string)($captions[$i] ?? '')), 0, SN_MAX_CAPTION),
            'size' => (int)filesize($target),
        ];
    }
    return [$saved, null];
}

/** Removes every stored variant of an attachment (original, WebP, optimised MP4, temp file). */
/** Parses php.ini sizes such as "8M". */
function sn_ini_bytes(string $value): int
{
    $value = trim($value);
    $number = (float)$value;
    switch (strtolower(substr($value, -1))) {
        case 'g': return (int)($number * 1073741824);
        case 'm': return (int)($number * 1048576);
        case 'k': return (int)($number * 1024);
    }
    return (int)$number;
}

/**
 * When a request is bigger than post_max_size PHP throws the whole form away, so type and id are
 * empty. Returns a clear message in that case, null otherwise.
 */
function sn_upload_overflow(): ?string
{
    $length = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    $limit = sn_ini_bytes((string)ini_get('post_max_size'));
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $limit > 0 && $length > $limit && !$_POST && !$_FILES) {
        return sn_t('e_toobig', sn_bytes($limit));
    }
    return null;
}

function sn_remove_files(array $evidence): void
{
    foreach ($evidence as $ev) {
        if (preg_match('/^[a-f0-9]{16}$/', (string)($ev['id'] ?? ''))) {
            foreach (glob(SN_EVIDENCE . '/' . $ev['id'] . '.*') ?: [] as $file) {
                @unlink($file);
            }
        }
    }
}

/** Path of the stored file. An optimised video lives at <id>.mp4, next to or instead of the original. */
function sn_ev_path(array $ev): string
{
    $id = (string)($ev['id'] ?? '');
    if (preg_match('/^[a-f0-9]{16}$/', $id) && ($ev['kind'] ?? '') === 'file' && is_file(SN_EVIDENCE . '/' . $id . '.mp4')) {
        return SN_EVIDENCE . '/' . $id . '.mp4';
    }
    return SN_EVIDENCE . '/' . basename((string)($ev['file'] ?? ''));
}

/** Download name with the extension of the file that is really stored. */
function sn_ev_name(array $ev, string $path): string
{
    return pathinfo((string)$ev['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($path, PATHINFO_EXTENSION));
}

function sn_gif_animated(string $path): bool
{
    $data = (string)@file_get_contents($path);
    return preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $data) > 1;
}

/** Converts a JPEG, PNG or still GIF to WebP with GD. Returns false when it cannot (the original is kept). */
function sn_webp(string $src, string $ext, string $dest): bool
{
    if (!function_exists('imagewebp')) {
        return false;
    }
    $info = @getimagesize($src);
    if (!$info || $info[0] * $info[1] > 40000000 || ($ext === 'gif' && sn_gif_animated($src))) {
        return false;
    }
    $image = match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($src),
        'png' => @imagecreatefrompng($src),
        'gif' => @imagecreatefromgif($src),
        default => false,
    };
    if (!$image) {
        return false;
    }
    if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
        $orientation = (int)(@exif_read_data($src)['Orientation'] ?? 1);
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
        if ($angle && ($rotated = imagerotate($image, $angle, 0))) {
            $image = $rotated;
        }
    }
    imagepalettetotruecolor($image);
    imagealphablending($image, true);
    imagesavealpha($image, true);
    $ok = @imagewebp($image, $dest, 82);
    return $ok && is_file($dest) && filesize($dest) > 0;
}

function sn_ffmpeg(): ?string
{
    static $binary = false;
    if ($binary !== false) {
        return $binary;
    }
    $binary = null;
    $disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
    if (DIRECTORY_SEPARATOR === '/' && function_exists('exec') && !in_array('exec', $disabled, true)) {
        @exec('command -v ffmpeg 2>/dev/null', $out, $code);
        if ($code === 0 && !empty($out[0])) {
            $binary = trim($out[0]);
        }
    }
    return $binary;
}

/**
 * Re-encodes a video to a smaller MP4 (H.264, AAC, at most 720p) in a background process so the upload
 * request is not held up. The result replaces the original only if it is smaller. Needs ffmpeg and exec().
 */
function sn_video_script(string $ffmpeg, string $path, string $id): string
{
    $tmp = SN_EVIDENCE . '/' . $id . '.tmp.mp4';
    $final = SN_EVIDENCE . '/' . $id . '.mp4';
    $q = 'escapeshellarg';
    return 'N=""; command -v nice >/dev/null 2>&1 && N="nice -n 19"; '
        . '( $N ' . $q($ffmpeg) . ' -nostdin -v error -i ' . $q($path)
        . ' -vf ' . $q('scale=-2:min(720\,ih)') . ' -c:v libx264 -preset veryfast -crf 28 -pix_fmt yuv420p'
        . ' -c:a aac -b:a 96k -movflags +faststart -f mp4 -y ' . $q($tmp)
        . ' && [ -f ' . $q($path) . ' ] && [ "$(wc -c < ' . $q($tmp) . ')" -lt "$(wc -c < ' . $q($path) . ')" ]'
        . ' && mv -f ' . $q($tmp) . ' ' . $q($final)
        . ' && { [ ' . $q($path) . ' = ' . $q($final) . ' ] || rm -f ' . $q($path) . '; } ; rm -f ' . $q($tmp) . ' )';
}

function sn_optimize_video(string $path, string $id): bool
{
    $ffmpeg = sn_ffmpeg();
    if (!$ffmpeg) {
        return false;
    }
    @exec('nohup sh -c ' . escapeshellarg(sn_video_script($ffmpeg, $path, $id)) . ' > /dev/null 2>&1 &');
    return true;
}

function sn_return_url(string $type, int $id): string
{
    $target = (string)($_POST['return'] ?? '');
    if ($target === '' && !empty($_SERVER['HTTP_REFERER'])) {
        // A dropped (too large) POST has no fields: go back to the page the form was on
        $ref = parse_url((string)$_SERVER['HTTP_REFERER']);
        if (($ref['host'] ?? '') === strtok((string)($_SERVER['HTTP_HOST'] ?? ''), ':')) {
            $target = ($ref['path'] ?? '/') . (isset($ref['query']) ? '?' . $ref['query'] : '');
        }
    }
    $parts = parse_url($target);
    if ($parts !== false && isset($parts['path']) && !isset($parts['host']) && !isset($parts['scheme'])
        && strncmp($parts['path'], '/', 1) === 0 && strpos($parts['path'], '//') !== 0 && strpos($parts['path'], '\\') === false) {
        return $parts['path'] . (isset($parts['query']) ? '?' . preg_replace('/[\r\n]/', '', $parts['query']) : '') . '#case-evidence';
    }
    return sn_base() . '/detail?type=' . $type . '&id=' . $id . '#case-evidence';
}

/** Applies one POSTed action. Returns [succeeded, translated message]. */
function sn_apply_post(): array
{
    if ($overflow = sn_upload_overflow()) {
        return [false, $overflow];
    }
    $type = (string)($_POST['type'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    $user = sn_user();

    if (!$user || !in_array($type, SN_TYPES, true) || $id <= 0
        || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)($_POST['csrf_token'] ?? ''))) {
        return [false, sn_t('e_generic')];
    }

    $action = (string)($_POST['action'] ?? '');
    $noteId = (string)($_POST['note_id'] ?? '');
    $key = sn_key($type, $id);
    $text = trim((string)($_POST['text'] ?? ''));
    $captions = (array)($_POST['caption'] ?? []);
    $oldCaptions = (array)($_POST['ev_caption'] ?? []);
    $error = null;
    $orphans = [];
    $done = ['add' => 'ok_added', 'edit' => 'ok_updated', 'delete' => 'ok_deleted', 'delete_evidence' => 'ok_img', 'appeal' => 'ok_appeal'];

    if (!isset($done[$action])) {
        return [false, sn_t('e_generic')];
    }
    if (in_array($action, ['add', 'appeal'], true) && !sn_can($user, 'add')) {
        return [false, sn_t('e_perm')];
    }
    if (in_array($action, ['add', 'edit'], true) && ($text === '' || mb_strlen($text) > SN_MAX_TEXT)) {
        return [false, sn_t('e_text', SN_MAX_TEXT)];
    }

    $result = sn_mutate(function (array &$data) use ($action, $key, $noteId, $text, $user, $captions, $oldCaptions, &$error, &$orphans) {
        if ($action === 'appeal') {
            $status = (string)($_POST['status'] ?? 'none');
            $line = mb_substr(trim(preg_replace('/\s+/', ' ', (string)($_POST['line'] ?? ''))), 0, SN_MAX_APPEAL);
            if (!in_array($status, SN_APPEAL, true)) {
                $error = sn_t('e_generic');
                return false;
            }
            if ($status === 'none' && $line === '') {
                unset($data['appeals'][$key]);
            } else {
                $data['appeals'][$key] = ['status' => $status, 'line' => $line, 'by' => $user['name'], 'at' => date('c')];
            }
            return true;
        }

        $list = $data['notes'][$key] ?? [];
        $index = null;
        foreach ($list as $i => $note) {
            if (($note['id'] ?? '') === $noteId) {
                $index = $i;
            }
        }
        if ($action !== 'add') {
            if ($index === null) {
                $error = sn_t('e_note');
                return false;
            }
            if (!sn_can($user, 'edit', $list[$index])) {
                $error = sn_t('e_own');
                return false;
            }
        }

        if ($action === 'delete') {
            $orphans = $list[$index]['evidence'] ?? [];
            array_splice($list, $index, 1);
        } elseif ($action === 'delete_evidence') {
            $evidenceId = (string)($_POST['evidence_id'] ?? '');
            $found = false;
            foreach ($list[$index]['evidence'] ?? [] as $j => $ev) {
                if ($ev['id'] === $evidenceId) {
                    $orphans = [$ev];
                    array_splice($list[$index]['evidence'], $j, 1);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $error = sn_t('e_note');
                return false;
            }
        } else {
            $have = $action === 'edit' ? count($list[$index]['evidence'] ?? []) : 0;
            [$saved, $uploadError] = sn_store_evidence(SN_MAX_PER_NOTE - $have, $captions);
            if ($uploadError !== null) {
                $orphans = $saved;
                $error = $uploadError;
                return false;
            }
            if ($action === 'add') {
                $list[] = [
                    'id' => bin2hex(random_bytes(6)),
                    'author' => $user['name'],
                    'author_id' => $user['id'],
                    'text' => $text,
                    'evidence' => $saved,
                    'created' => date('c'),
                    'updated' => null,
                    'public' => ($_POST['public'] ?? '') === '1',
                ];
            } else {
                foreach ($list[$index]['evidence'] ?? [] as $j => $ev) {
                    if (isset($oldCaptions[$ev['id']])) {
                        $list[$index]['evidence'][$j]['caption'] = mb_substr(trim((string)$oldCaptions[$ev['id']]), 0, SN_MAX_CAPTION);
                    }
                }
                $list[$index]['text'] = $text;
                $list[$index]['evidence'] = array_merge($list[$index]['evidence'] ?? [], $saved);
                $list[$index]['updated'] = date('c');
                $list[$index]['public'] = ($_POST['public'] ?? '') === '1';
            }
        }

        if ($list) {
            $data['notes'][$key] = array_values($list);
        } else {
            unset($data['notes'][$key]);
        }
        return true;
    });

    sn_remove_files($orphans);
    if ($result === false) {
        return [false, $error ?? sn_t('e_save')];
    }
    return [true, sn_t($done[$action])];
}

/** Handles a form post from the detail page (redirect back) or an XHR (JSON). */
function sn_handle_post(): void
{
    [$ok, $message] = sn_apply_post();
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $type = (string)($_POST['type'] ?? '');
    sn_flash($ok ? 'ok' : 'err', $message);
    header('Location: ' . sn_return_url(in_array($type, SN_TYPES, true) ? $type : 'ban', max((int)($_POST['id'] ?? 0), 0)));
    exit;
}

/** Streams an attachment to a logged-in staff member, with Range support for video seeking. */
function sn_serve_evidence(string $evidenceId): void
{
    $isStaff = sn_user() !== null;
    session_write_close();
    if (preg_match('/^[a-f0-9]{16}$/', $evidenceId)) {
        foreach (sn_read()['notes'] as $notes) {
            foreach ($notes as $note) {
                foreach ($note['evidence'] ?? [] as $ev) {
                    $path = sn_ev_path($ev);
                    if ($ev['id'] !== $evidenceId || !is_file($path)) {
                        continue;
                    }
                    if (!$isStaff && empty($note['public'])) {
                        http_response_code(403);
                        exit;
                    }
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $size = filesize($path);
                    $start = 0;
                    $end = $size - 1;
                    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m) && ($m[1] !== '' || $m[2] !== '')) {
                        $start = $m[1] !== '' ? (int)$m[1] : max(0, $size - (int)$m[2]);
                        $end = $m[1] !== '' && $m[2] !== '' ? min((int)$m[2], $size - 1) : $size - 1;
                        if ($start > $end || $start >= $size) {
                            http_response_code(416);
                            header('Content-Range: bytes */' . $size);
                            exit;
                        }
                        http_response_code(206);
                        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
                    }
                    $inline = ($ev['kind'] ?? 'image') === 'image' || in_array($ext, ['mp4', 'webm', 'm4v'], true);
                    header('Content-Type: ' . sn_mime($ext));
                    header('Content-Length: ' . ($end - $start + 1));
                    header('Accept-Ranges: bytes');
                    header('X-Content-Type-Options: nosniff');
                    header('Cache-Control: ' . (empty($note['public']) ? 'private' : 'public') . ', max-age=3600');
                    header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . str_replace('"', '', sn_ev_name($ev, $path)) . '"');
                    $fh = fopen($path, 'rb');
                    fseek($fh, $start);
                    $left = $end - $start + 1;
                    while ($left > 0 && !feof($fh)) {
                        $chunk = fread($fh, min(1048576, $left));
                        echo $chunk;
                        $left -= strlen($chunk);
                        flush();
                    }
                    fclose($fh);
                    exit;
                }
            }
        }
    }
    http_response_code(404);
    exit;
}

/** Summary of one case for the admin search: report and attachment counts plus appeal status. */
function sn_case_summary(string $type, int $id, ?array $data = null): array
{
    $data = $data ?? sn_read();
    $key = sn_key($type, $id);
    $notes = $data['notes'][$key] ?? [];
    return [
        'reports' => count($notes),
        'files' => array_sum(array_map(fn($n) => count($n['evidence'] ?? []), $notes)),
        'appeal' => $data['appeals'][$key]['status'] ?? 'none',
    ];
}

/** Cases whose reports, captions, file names or appeal line contain the query. Returns [[type, id], ...]. */
function sn_search_cases(string $query): array
{
    $data = sn_read();
    $hits = [];
    $match = fn($text) => $text !== '' && mb_stripos((string)$text, $query) !== false;
    foreach ($data['notes'] as $key => $notes) {
        foreach ($notes as $note) {
            $haystack = [$note['text'] ?? '', $note['author'] ?? ''];
            foreach ($note['evidence'] ?? [] as $ev) {
                $haystack[] = $ev['caption'] ?? '';
                $haystack[] = $ev['name'] ?? '';
            }
            if (array_filter($haystack, $match)) {
                $hits[$key] = true;
            }
        }
    }
    foreach ($data['appeals'] as $key => $appeal) {
        if ($match($appeal['line'] ?? '')) {
            $hits[$key] = true;
        }
    }
    $out = [];
    foreach (array_keys($hits) as $key) {
        [$type, $id] = explode('_', $key) + [1 => '0'];
        $out[] = [$type, (int)$id];
    }
    return $out;
}

/** Everything the admin "Modify" dialog needs to show about a case. */
function sn_case_json(string $type, int $id): array
{
    $data = sn_read();
    $key = sn_key($type, $id);
    return [
        'csrf' => sn_csrf(),
        'can_add' => sn_can(sn_user(), 'add'),
        'appeal' => $data['appeals'][$key] ?? null,
        'notes' => array_map(fn($n) => [
            'id' => $n['id'],
            'author' => $n['author'],
            'text' => $n['text'],
            'created' => sn_date($n['created']),
            'attachments' => array_map(fn($e) => ['name' => $e['name'], 'caption' => $e['caption'] ?? '', 'kind' => $e['kind'] ?? 'image'], $n['evidence'] ?? []),
        ], $data['notes'][$key] ?? []),
    ];
}

/** Styles and the caption helper, emitted once per page. Uses the site's theme variables. */
function sn_css(): string
{
    static $done = false;
    if ($done) {
        return '';
    }
    $done = true;
    return <<<'HTML'
<style>
.sn{--sn-line:var(--border-color,#d5d9de);--sn-mute:var(--text-tertiary,#5b6472);--sn-red:var(--primary,#b3261e);
  --sn-tone-none:var(--border-strong,#b8bec6);--sn-tone-pending:#c98a12;--sn-tone-accepted:#1f8a5b;--sn-tone-rejected:var(--primary,#b3261e);
  color:var(--text-primary,#14181f);font-size:.9375rem;line-height:1.55}
.sn *,.sn *::before,.sn *::after{box-sizing:border-box}
.sn-card{background:var(--card-bg,#fff);border:1px solid var(--sn-line);border-radius:8px;margin-bottom:1.5rem}
.sn-head{display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:.25rem 1rem;padding:1rem 1.25rem;border-bottom:1px solid var(--sn-line)}
.sn-head h2,.sn-head h5{margin:0;font-family:var(--font-display,inherit);font-size:1.05rem;font-weight:700;letter-spacing:-.01em}
.sn-body{padding:1.25rem}
.sn-muted{color:var(--sn-mute);font-size:.8125rem}
.sn p{margin:0 0 .75rem}
.sn-appeal{display:flex;flex-wrap:wrap;align-items:center;gap:.5rem .75rem;border-left:4px solid var(--tone,var(--sn-tone-none));background:var(--bg-secondary,#eceef0);border-radius:0 6px 6px 0;padding:.75rem 1rem;margin-bottom:1.25rem}
.sn-appeal-label{font-weight:700;font-family:var(--font-display,inherit)}
.sn-appeal form{display:flex;flex:1 1 320px;flex-wrap:wrap;gap:.5rem}
.sn-appeal .sn-input[name=line]{flex:1 1 200px}
.sn-appeal .sn-input[name=status]{flex:0 0 auto;width:auto}
.sn-tag{display:inline-block;border:1px solid var(--tone,var(--sn-tone-none));color:var(--tone,var(--sn-mute));border-radius:4px;padding:.05rem .5rem;font-size:.8125rem;font-weight:600;white-space:nowrap}
.sn-entry{border-top:1px solid var(--sn-line);padding:1rem 0}
.sn-entry:first-of-type{border-top:0;padding-top:0}
.sn-meta{display:flex;justify-content:space-between;flex-wrap:wrap;gap:.25rem 1rem;margin-bottom:.5rem}
.sn-text{white-space:pre-wrap;max-width:75ch}
.sn-figs{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.75rem;margin:.25rem 0 1rem}
.sn-fig{margin:0}
.sn-fig img{display:block;width:100%;height:110px;object-fit:cover;border:1px solid var(--sn-line);border-radius:4px;background:var(--bg-secondary,#eceef0)}
.sn-fig video{display:block;width:100%;max-height:150px;background:#000;border-radius:4px}
.sn-fig figcaption{font-size:.8125rem;color:var(--sn-mute);margin-top:.25rem;word-break:break-word}
.sn-chip{display:flex;align-items:center;gap:.6rem;border:1px solid var(--sn-line);border-radius:6px;padding:.55rem .75rem;color:var(--text-primary,#14181f);text-decoration:none;word-break:break-all}
.sn-chip i{font-size:1.25rem;color:var(--sn-red)}
.sn-label{display:block;font-weight:600;margin:0 0 .25rem}
.sn-input{display:block;width:100%;padding:.55rem .7rem;font:inherit;color:var(--text-primary,#14181f);background:var(--input-bg,var(--card-bg,#fff));border:1px solid var(--input-border,var(--sn-line));border-radius:6px}
.sn-input:focus-visible,.sn-btn:focus-visible,.sn a:focus-visible,.sn summary:focus-visible{outline:2px solid var(--sn-red);outline-offset:2px}
textarea.sn-input{resize:vertical}
.sn-captions{display:grid;gap:.5rem;margin:.5rem 0}
.sn-captions:empty{display:none}
.sn-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;font:inherit;font-weight:600;color:#fff;background:var(--sn-red);border:1px solid var(--sn-red);border-radius:6px;cursor:pointer;text-decoration:none}
.sn-btn:hover{filter:brightness(.92);color:#fff}
.sn-btn--ghost{background:transparent;color:var(--text-primary,#14181f);border-color:var(--sn-line)}
.sn-btn--ghost:hover{color:var(--text-primary,#14181f);background:var(--hover-bg,rgba(0,0,0,.05));filter:none}
.sn-btn--danger{background:transparent;color:var(--sn-red)}
.sn-btn--danger:hover{background:var(--sn-red);color:#fff;filter:none}
.sn-btn--sm{padding:.3rem .7rem;font-size:.8125rem}
.sn-link{background:none;border:0;padding:0;font:inherit;font-size:.8125rem;color:var(--sn-red);cursor:pointer}
.sn details{margin-top:.25rem}
.sn summary{cursor:pointer;font-size:.8125rem;color:var(--sn-mute)}
.sn details form{margin-top:.75rem}
.sn-alert{border-left:4px solid var(--sn-tone-accepted);background:var(--bg-secondary,#eceef0);border-radius:0 6px 6px 0;padding:.6rem 1rem;margin-bottom:1rem}
.sn-alert--err{border-left-color:var(--sn-red)}
.sn-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1.5rem}
.sn-stat{background:var(--card-bg,#fff);border:1px solid var(--sn-line);border-left:4px solid var(--sn-red);border-radius:6px;padding:.9rem 1.1rem}
.sn-stat b{display:block;font-family:var(--font-display,inherit);font-size:1.9rem;line-height:1.1;letter-spacing:-.02em}
.sn-table{width:100%;border-collapse:collapse}
.sn-table th{text-align:start;font-weight:600;font-size:.8125rem;color:var(--sn-mute);padding:.5rem .75rem;border-bottom:1px solid var(--sn-line)}
.sn-table td{padding:.65rem .75rem;border-bottom:1px solid var(--sn-line);vertical-align:top}
.sn-table tr:last-child td{border-bottom:0}
.sn-scroll{overflow-x:auto}
.sn-addgrid{display:grid;grid-template-columns:160px 160px 1fr;gap:.75rem;margin-bottom:1rem}
@media (max-width:640px){.sn-addgrid{grid-template-columns:1fr}}
.sn-filter{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem}
.sn-filter button{padding:.3rem .8rem;border:1px solid var(--sn-line);border-radius:999px;background:transparent;color:var(--text-primary,#14181f);font:inherit;font-size:.875rem;cursor:pointer}
.sn-filter button[aria-pressed=true]{background:var(--sn-red);border-color:var(--sn-red);color:#fff}
@media (prefers-reduced-motion:no-preference){.sn details[open] form{animation:sn-in .15s ease-out}}
@keyframes sn-in{from{opacity:0}to{opacity:1}}
</style>
<script>
document.addEventListener('change', function (e) {
  var input = e.target;
  if (!input.matches || !input.matches('input[type=file][data-sn-captions]')) { return; }
  var box = input.form.querySelector('[data-sn-caption-list]');
  if (!box) { return; }
  box.textContent = '';
  Array.prototype.forEach.call(input.files, function (file) {
    var field = document.createElement('input');
    field.type = 'text'; field.name = 'caption[]'; field.maxLength = 120; field.className = 'sn-input';
    field.placeholder = file.name + ': ' + input.getAttribute('data-placeholder');
    box.appendChild(field);
  });
});
</script>
HTML;
}

function sn_appeal_tag(?array $appeal): string
{
    $status = $appeal['status'] ?? 'none';
    return '<span class="sn-tag" style="--tone:var(--sn-tone-' . sn_e($status) . ')">' . sn_e(sn_t('ap_' . $status)) . '</span>';
}

function sn_file_icon(string $ext): string
{
    return in_array($ext, ['dem', 'demo'], true) ? 'fa-gamepad' : (str_starts_with(sn_mime($ext), 'audio') ? 'fa-music' : 'fa-film');
}

/** Panel for the punishment detail page. Empty string for anyone who is not logged in as staff. */
/** Public reports of a punishment for visitors: text and files, no author names, no appeal. */
function sn_public_html(string $type, int $id): string
{
    if (!in_array($type, SN_TYPES, true) || $id <= 0) {
        return '';
    }
    $notes = array_values(array_filter(sn_read()['notes'][sn_key($type, $id)] ?? [], fn($n) => !empty($n['public'])));
    if (!$notes) {
        return '';
    }
    $base = sn_base();

    ob_start();
    echo sn_css();
    ?>
    <section class="sn sn-card" id="case-evidence" dir="<?= sn_lang() === 'ar' ? 'rtl' : 'ltr' ?>">
        <header class="sn-head"><h5><i class="fas fa-folder-open"></i> <?= sn_e(sn_t('public_title')) ?></h5></header>
        <div class="sn-body">
            <?php foreach ($notes as $note): ?>
                <article class="sn-entry">
                    <div class="sn-meta"><span class="sn-muted"><?= sn_e(sn_date($note['created'])) ?></span></div>
                    <p class="sn-text"><?= sn_e($note['text']) ?></p>
                    <?php if (!empty($note['evidence'])): ?>
                        <div class="sn-figs">
                            <?php foreach ($note['evidence'] as $ev):
                                $src = sn_e($base . '/demos/case-evidence.php?evidence=' . $ev['id']);
                                $evPath = sn_ev_path($ev);
                                $ext = strtolower(pathinfo($evPath, PATHINFO_EXTENSION));
                                ?>
                                <figure class="sn-fig">
                                    <?php if (($ev['kind'] ?? 'image') === 'image'): ?>
                                        <a href="<?= $src ?>" target="_blank" rel="noopener"><img src="<?= $src ?>" alt="<?= sn_e(($ev['caption'] ?? '') ?: $ev['name']) ?>" loading="lazy"></a>
                                    <?php elseif (in_array($ext, ['mp4', 'webm', 'm4v'], true)): ?>
                                        <video controls preload="none" src="<?= $src ?>"></video>
                                    <?php else: ?>
                                        <a class="sn-chip" href="<?= $src ?>" download="<?= sn_e(sn_ev_name($ev, $evPath)) ?>"><i class="fas <?= sn_file_icon($ext) ?>"></i><span><?= sn_e(sn_ev_name($ev, $evPath)) ?></span></a>
                                    <?php endif; ?>
                                    <?php if (!empty($ev['caption'])): ?><figcaption><?= sn_e($ev['caption']) ?></figcaption><?php endif; ?>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return (string)ob_get_clean();
}

/** Panel for the punishment detail page: staff get the full case, everyone else the public reports. */
function sn_panel_html(string $type, int $id): string
{
    $user = sn_user();
    if (!$user) {
        return sn_public_html($type, $id);
    }
    if (!in_array($type, SN_TYPES, true) || $id <= 0) {
        return '';
    }
    $data = sn_read();
    $key = sn_key($type, $id);
    $notes = $data['notes'][$key] ?? [];
    $appeal = $data['appeals'][$key] ?? null;
    $status = $appeal['status'] ?? 'none';
    $canAdd = sn_can($user, 'add');
    $base = sn_base();
    $csrf = sn_csrf();
    $return = (string)($_SERVER['REQUEST_URI'] ?? '');
    $endpoint = sn_e($base . '/demos/case-evidence.php');
    $flash = $_SESSION['sn_flash'] ?? null;
    unset($_SESSION['sn_flash']);
    $accept = SN_ACCEPT;

    $hidden = function (string $act, string $noteId = '') use ($type, $id, $csrf, $return) {
        return '<input type="hidden" name="action" value="' . $act . '">'
            . '<input type="hidden" name="type" value="' . sn_e($type) . '">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<input type="hidden" name="note_id" value="' . sn_e($noteId) . '">'
            . '<input type="hidden" name="csrf_token" value="' . sn_e($csrf) . '">'
            . '<input type="hidden" name="return" value="' . sn_e($return) . '">';
    };

    ob_start();
    echo sn_css();
    ?>
    <section class="sn sn-card" id="case-evidence" dir="<?= sn_lang() === 'ar' ? 'rtl' : 'ltr' ?>">
        <header class="sn-head">
            <h5><i class="fas fa-folder-open"></i> <?= sn_e(sn_t('tab')) ?></h5>
            <span class="sn-muted"><i class="fas fa-lock"></i> <?= sn_e(sn_t('private')) ?></span>
        </header>
        <div class="sn-body">
            <?php if ($flash): ?>
                <div class="sn-alert <?= $flash[0] === 'ok' ? '' : 'sn-alert--err' ?>" role="status"><?= sn_e($flash[1]) ?></div>
            <?php endif; ?>

            <div class="sn-appeal" style="--tone:var(--sn-tone-<?= sn_e($status) ?>)">
                <span class="sn-appeal-label"><?= sn_e(sn_t('ap_title')) ?></span>
                <?php if ($canAdd): ?>
                    <form method="post" action="<?= $endpoint ?>">
                        <?= $hidden('appeal') ?>
                        <select name="status" class="sn-input" aria-label="<?= sn_e(sn_t('ap_title')) ?>">
                            <?php foreach (SN_APPEAL as $option): ?>
                                <option value="<?= $option ?>" <?= $option === $status ? 'selected' : '' ?>><?= sn_e(sn_t('ap_' . $option)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="line" class="sn-input" maxlength="<?= SN_MAX_APPEAL ?>"
                               value="<?= sn_e($appeal['line'] ?? '') ?>" placeholder="<?= sn_e(sn_t('ap_line_ph')) ?>" aria-label="<?= sn_e(sn_t('ap_line_ph')) ?>">
                        <button class="sn-btn sn-btn--ghost" type="submit"><?= sn_e(sn_t('ap_save')) ?></button>
                    </form>
                <?php else: ?>
                    <?= sn_appeal_tag($appeal) ?>
                    <?php if (!empty($appeal['line'])): ?><span><?= sn_e($appeal['line']) ?></span><?php endif; ?>
                <?php endif; ?>
                <?php if ($appeal): ?>
                    <span class="sn-muted" style="flex-basis:100%"><?= sn_e(sn_t('by')) ?> <?= sn_e($appeal['by'] ?? '') ?>, <?= sn_e(sn_date($appeal['at'] ?? '')) ?></span>
                <?php endif; ?>
            </div>

            <?php if (!$notes): ?>
                <p class="sn-muted"><?= sn_e(sn_t('empty')) ?></p>
            <?php endif; ?>

            <?php foreach ($notes as $note): $canEdit = sn_can($user, 'edit', $note); ?>
                <article class="sn-entry">
                    <div class="sn-meta">
                        <strong><i class="fas fa-user-shield"></i> <?= sn_e($note['author']) ?></strong>
                        <span class="sn-muted">
                            <?php if (!empty($note['public'])): ?><span class="sn-tag" style="--tone:var(--sn-tone-accepted)"><?= sn_e(sn_t('public_badge')) ?></span> <?php endif; ?>
                            <?= sn_e(sn_date($note['created'])) ?><?php if (!empty($note['updated'])): ?>, <?= sn_e(sn_t('edited')) ?> <?= sn_e(sn_date($note['updated'])) ?><?php endif; ?>
                        </span>
                    </div>
                    <p class="sn-text"><?= sn_e($note['text']) ?></p>

                    <?php if (!empty($note['evidence'])): ?>
                        <div class="sn-figs">
                            <?php foreach ($note['evidence'] as $ev):
                                $src = sn_e($base . '/demos/case-evidence.php?evidence=' . $ev['id']);
                                $evPath = sn_ev_path($ev);
                                $ext = strtolower(pathinfo($evPath, PATHINFO_EXTENSION));
                                $isImage = ($ev['kind'] ?? 'image') === 'image';
                                $playable = in_array($ext, ['mp4', 'webm', 'm4v'], true);
                                ?>
                                <figure class="sn-fig">
                                    <?php if ($isImage): ?>
                                        <a href="<?= $src ?>" target="_blank" rel="noopener"><img src="<?= $src ?>" alt="<?= sn_e(($ev['caption'] ?? '') ?: $ev['name']) ?>" loading="lazy"></a>
                                    <?php elseif ($playable): ?>
                                        <video controls preload="none" src="<?= $src ?>"></video>
                                    <?php else: ?>
                                        <a class="sn-chip" href="<?= $src ?>" download="<?= sn_e(sn_ev_name($ev, $evPath)) ?>"><i class="fas <?= sn_file_icon($ext) ?>"></i><span><?= sn_e(sn_ev_name($ev, $evPath)) ?><br><span class="sn-muted"><?= sn_e(sn_bytes(is_file($evPath) ? (int)filesize($evPath) : (int)$ev['size'])) ?></span></span></a>
                                    <?php endif; ?>
                                    <figcaption>
                                        <?= sn_e($ev['caption'] ?? '') ?>
                                        <?php if ($canEdit): ?>
                                            <form method="post" action="<?= $endpoint ?>" onsubmit="return confirm(<?= sn_e(json_encode(sn_t('confirm_img'), JSON_UNESCAPED_UNICODE)) ?>)">
                                                <?= $hidden('delete_evidence', $note['id']) ?>
                                                <input type="hidden" name="evidence_id" value="<?= sn_e($ev['id']) ?>">
                                                <button class="sn-link" type="submit"><?= sn_e(sn_t('remove')) ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </figcaption>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($canEdit): ?>
                        <details>
                            <summary><?= sn_e(sn_t('edit')) ?></summary>
                            <form method="post" action="<?= $endpoint ?>" enctype="multipart/form-data">
                                <?= $hidden('edit', $note['id']) ?>
                                <textarea name="text" class="sn-input" rows="4" maxlength="<?= SN_MAX_TEXT ?>" required><?= sn_e($note['text']) ?></textarea>
                                <?php foreach ($note['evidence'] ?? [] as $ev): ?>
                                    <input type="text" name="ev_caption[<?= sn_e($ev['id']) ?>]" class="sn-input" style="margin-top:.5rem" maxlength="<?= SN_MAX_CAPTION ?>"
                                           value="<?= sn_e($ev['caption'] ?? '') ?>" placeholder="<?= sn_e($ev['name'] . ': ' . sn_t('caption_ph')) ?>">
                                <?php endforeach; ?>
                                <label class="sn-muted" style="display:flex;gap:.5rem;align-items:center;margin-top:.5rem"><input type="checkbox" name="public" value="1" <?= !empty($note['public']) ? 'checked' : '' ?>> <?= sn_e(sn_t('public_label')) ?></label>
                                <input type="file" name="evidence[]" class="sn-input" style="margin-top:.5rem" accept="<?= $accept ?>" multiple
                                       data-sn-captions data-placeholder="<?= sn_e(sn_t('caption_ph')) ?>">
                                <div class="sn-captions" data-sn-caption-list></div>
                                <button class="sn-btn sn-btn--sm" type="submit"><?= sn_e(sn_t('save')) ?></button>
                            </form>
                            <form method="post" action="<?= $endpoint ?>" onsubmit="return confirm(<?= sn_e(json_encode(sn_t('confirm_note'), JSON_UNESCAPED_UNICODE)) ?>)">
                                <?= $hidden('delete', $note['id']) ?>
                                <button class="sn-btn sn-btn--danger sn-btn--sm" type="submit"><?= sn_e(sn_t('del')) ?></button>
                            </form>
                        </details>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

            <?php if ($canAdd): ?>
                <form method="post" action="<?= $endpoint ?>" enctype="multipart/form-data" style="margin-top:1rem">
                    <?= $hidden('add') ?>
                    <label class="sn-label" for="sn-text"><?= sn_e(sn_t('report')) ?></label>
                    <textarea id="sn-text" name="text" class="sn-input" rows="4" maxlength="<?= SN_MAX_TEXT ?>" required
                              placeholder="<?= sn_e(sn_t('report_ph')) ?>"></textarea>
                    <label class="sn-label" for="sn-files" style="margin-top:1rem"><?= sn_e(sn_t('shots')) ?></label>
                    <input id="sn-files" type="file" name="evidence[]" class="sn-input" accept="<?= $accept ?>" multiple
                           data-sn-captions data-placeholder="<?= sn_e(sn_t('caption_ph')) ?>">
                    <div class="sn-captions" data-sn-caption-list></div>
                    <div class="sn-muted" style="margin:.25rem 0 .75rem"><?= sn_e(sn_t('shots_hint')) ?></div>
                    <label class="sn-muted" style="display:flex;gap:.5rem;align-items:center;margin-bottom:1rem"><input type="checkbox" name="public" value="1"> <?= sn_e(sn_t('public_label')) ?></label>
                    <button class="sn-btn" type="submit"><i class="fas fa-plus"></i> <?= sn_e(sn_t('add')) ?></button>
                </form>
            <?php else: ?>
                <p class="sn-muted"><?= sn_e(sn_t('readonly')) ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return (string)ob_get_clean();
}

/** One row per punishment that has a report or an appeal outcome, newest activity first. */
function sn_rows(): array
{
    $data = sn_read();
    $rows = [];
    foreach (array_unique(array_merge(array_keys($data['notes']), array_keys($data['appeals']))) as $key) {
        [$type, $id] = explode('_', $key) + [1 => '0'];
        $notes = $data['notes'][$key] ?? [];
        $appeal = $data['appeals'][$key] ?? null;
        $last = $notes ? end($notes) : null;
        $rows[] = [
            'type' => $type, 'id' => (int)$id, 'appeal' => $appeal, 'latest' => $last,
            'when' => max($last['created'] ?? '', $appeal['at'] ?? ''),
            'files' => array_sum(array_map(fn($n) => count($n['evidence'] ?? []), $notes)),
        ];
    }
    usort($rows, fn($a, $b) => strcmp($b['when'], $a['when']));
    return $rows;
}

function sn_stats(): array
{
    $data = sn_read();
    $notes = array_merge([], ...array_values($data['notes']));
    return [
        'reports' => count($notes),
        'files' => array_sum(array_map(fn($n) => count($n['evidence'] ?? []), $notes)),
        'pending' => count(array_filter($data['appeals'], fn($a) => ($a['status'] ?? '') === 'pending')),
    ];
}

/** The "Case Evidence" tab for templates/admin/dashboard.php: overview, add to a case, all cases. */
function sn_admin_pane_html(): string
{
    $user = sn_user();
    if (!$user) {
        return '';
    }
    $base = sn_base();
    $stats = sn_stats();
    $rows = sn_rows();
    $canAdd = sn_can($user, 'add');

    ob_start();
    echo sn_css();
    ?>
    <div class="tab-pane fade" id="case-evidence" role="tabpanel" data-refresh-label="<?= sn_e(sn_t('refresh')) ?>">
        <div class="sn" dir="<?= sn_lang() === 'ar' ? 'rtl' : 'ltr' ?>">
            <div class="sn-stats">
                <div class="sn-stat"><b><?= $stats['reports'] ?></b><?= sn_e(sn_t('a_reports')) ?></div>
                <div class="sn-stat"><b><?= $stats['files'] ?></b><?= sn_e(sn_t('a_att')) ?></div>
                <div class="sn-stat" style="border-left-color:var(--sn-tone-pending)"><b><?= $stats['pending'] ?></b><?= sn_e(sn_t('a_pending')) ?></div>
            </div>

            <?php if ($canAdd): ?>
            <section class="sn-card">
                <header class="sn-head"><h5><i class="fas fa-plus"></i> <?= sn_e(sn_t('c_add')) ?></h5></header>
                <div class="sn-body">
                    <div class="sn-alert sn-alert--err" data-sn-msg hidden role="alert"></div>
                    <form method="post" action="<?= sn_e($base . '/admin/case-evidence') ?>" enctype="multipart/form-data" data-sn-add>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="csrf_token" value="<?= sn_e(sn_csrf()) ?>">
                        <div class="sn-addgrid">
                            <div>
                                <label class="sn-label" for="sn-add-type"><?= sn_e(sn_t('c_type')) ?></label>
                                <select id="sn-add-type" name="type" class="sn-input">
                                    <?php foreach (SN_TYPES as $t): ?><option value="<?= $t ?>"><?= sn_e(sn_t('t_' . $t)) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="sn-label" for="sn-add-id"><?= sn_e(sn_t('c_id')) ?></label>
                                <input id="sn-add-id" name="id" type="number" min="1" class="sn-input" required>
                            </div>
                            <div>
                                <label class="sn-label" for="sn-add-files"><?= sn_e(sn_t('shots')) ?></label>
                                <input id="sn-add-files" type="file" name="evidence[]" class="sn-input" accept="<?= SN_ACCEPT ?>" multiple
                                       data-sn-captions data-placeholder="<?= sn_e(sn_t('caption_ph')) ?>">
                            </div>
                        </div>
                        <div class="sn-captions" data-sn-caption-list></div>
                        <label class="sn-label" for="sn-add-text"><?= sn_e(sn_t('report')) ?></label>
                        <textarea id="sn-add-text" name="text" class="sn-input" rows="3" maxlength="<?= SN_MAX_TEXT ?>" required placeholder="<?= sn_e(sn_t('report_ph')) ?>"></textarea>
                        <div class="sn-muted" style="margin:.25rem 0 .75rem"><?= sn_e(sn_t('shots_hint')) ?></div>
                        <label class="sn-muted" style="display:flex;gap:.5rem;align-items:center;margin-bottom:1rem"><input type="checkbox" name="public" value="1"> <?= sn_e(sn_t('public_label')) ?></label>
                        <button class="sn-btn" type="submit"><i class="fas fa-plus"></i> <?= sn_e(sn_t('c_add')) ?></button>
                    </form>
                </div>
            </section>
            <?php endif; ?>

            <section class="sn-card">
                <header class="sn-head"><h5><i class="fas fa-folder-open"></i> <?= sn_e(sn_t('a_latest')) ?></h5></header>
                <div class="sn-body">
                    <?php if (!$rows): ?>
                        <p class="sn-muted" style="margin:0"><?= sn_e(sn_t('a_empty')) ?></p>
                    <?php else: ?>
                        <div class="sn-filter" role="group" aria-label="<?= sn_e(sn_t('ap_title')) ?>">
                            <button type="button" data-sn-filter="" aria-pressed="true"><?= sn_e(sn_t('a_all')) ?></button>
                            <?php foreach (['pending', 'accepted', 'rejected'] as $option): ?>
                                <button type="button" data-sn-filter="<?= $option ?>" aria-pressed="false"><?= sn_e(sn_t('ap_' . $option)) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="sn-scroll">
                            <table class="sn-table">
                                <thead><tr>
                                    <th><?= sn_e(sn_t('a_punish')) ?></th><th><?= sn_e(sn_t('report')) ?></th><th><?= sn_e(sn_t('a_author')) ?></th>
                                    <th><?= sn_e(sn_t('a_att')) ?></th><th><?= sn_e(sn_t('ap_title')) ?></th><th><?= sn_e(sn_t('a_date')) ?></th>
                                </tr></thead>
                                <tbody>
                                <?php foreach ($rows as $r): ?>
                                    <tr data-appeal="<?= sn_e($r['appeal']['status'] ?? 'none') ?>">
                                        <td><a href="<?= sn_e($base . '/detail?type=' . $r['type'] . '&id=' . $r['id'] . '#case-evidence') ?>" title="<?= sn_e(sn_t('c_open')) ?>"><?= sn_e(sn_t('t_' . $r['type'])) ?> #<?= $r['id'] ?></a></td>
                                        <td><?= sn_e(mb_strimwidth($r['latest']['text'] ?? '', 0, 120, '...')) ?></td>
                                        <td><?= sn_e($r['latest']['author'] ?? '') ?></td>
                                        <td><?= $r['files'] ?></td>
                                        <td><?= sn_appeal_tag($r['appeal']) ?><?php if (!empty($r['appeal']['line'])): ?> <span class="sn-muted"><?= sn_e($r['appeal']['line']) ?></span><?php endif; ?></td>
                                        <td class="sn-muted"><?= sn_e(sn_date($r['when'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <script>
        (function () {
            var pane = document.getElementById('case-evidence');
            var tab = document.getElementById('case-evidence-tab');
            if (location.hash === '#case-evidence' && tab) {
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.bootstrap) { bootstrap.Tab.getOrCreateInstance(tab).show(); }
                });
            }
            pane.querySelectorAll('[data-sn-filter]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var wanted = button.getAttribute('data-sn-filter');
                    pane.querySelectorAll('[data-sn-filter]').forEach(function (b) { b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
                    pane.querySelectorAll('tr[data-appeal]').forEach(function (row) { row.hidden = wanted !== '' && row.getAttribute('data-appeal') !== wanted; });
                });
            });
            var form = pane.querySelector('form[data-sn-add]');
            if (!form) { return; }
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var message = pane.querySelector('[data-sn-msg]');
                var submit = form.querySelector('button[type=submit]');
                submit.disabled = true;
                fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (response) {
                        return response.text().then(function (text) {
                            try { return JSON.parse(text); } catch (e) { throw new Error('HTTP ' + response.status + ': ' + text.replace(/<[^>]*>/g, ' ').trim().slice(0, 160)); }
                        });
                    })
                    .then(function (result) {
                        message.textContent = result.message || result.error || 'Error';
                        message.classList.toggle('sn-alert--err', !result.ok);
                        if (result.ok) {
                            var refresh = document.createElement('button');
                            refresh.type = 'button';
                            refresh.className = 'sn-btn sn-btn--sm';
                            refresh.style.marginInlineStart = '.75rem';
                            refresh.textContent = pane.getAttribute('data-refresh-label');
                            refresh.addEventListener('click', function () { location.hash = '#case-evidence'; location.reload(); });
                            message.appendChild(refresh);
                            form.reset();
                            form.querySelector('[data-sn-caption-list]').textContent = '';
                        }
                        message.hidden = false;
                        submit.disabled = false;
                    })
                    .catch(function (error) { message.textContent = error.message; message.hidden = false; submit.disabled = false; });
            });
        })();
        </script>
    </div>
    <?php
    return (string)ob_get_clean();
}

// Direct request to this file: form posts and attachment downloads. Everything else goes to the admin tab.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        sn_handle_post();
    } elseif (isset($_GET['evidence'])) {
        sn_serve_evidence((string)$_GET['evidence']);
    } else {
        header('Location: ' . sn_base() . '/admin#case-evidence');
    }
}
