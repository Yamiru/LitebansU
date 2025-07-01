<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Sunucu cezalarını ve yasaklamalarını görüntülemek için herkese açık arayüz'
    ],
    
    'nav' => [
        'home' => 'Anasayfa',
        'bans' => 'Yasaklamalar',
        'mutes' => 'Susturmalar',
        'warnings' => 'Uyarılar',
        'kicks' => 'Atmalar',
        'statistics' => 'İstatistikler',
        'language' => 'Dil',
        'theme' => 'Tema',
        'admin' => 'Yönetim',
        'protest' => 'Yasaklama İtirazı',
    ],
    
    'home' => [
        'welcome' => 'Sunucu Cezaları',
        'description' => 'Oyuncu cezalarını arayın ve son etkinlikleri görüntüleyin',
        'recent_activity' => 'Son Etkinlikler',
        'recent_bans' => 'Son Yasaklamalar',
        'recent_mutes' => 'Son Susturmalar',
        'no_recent_bans' => 'Yakın zamanda uygulanmış bir yasaklama bulunamadı',
        'no_recent_mutes' => 'Yakın zamanda uygulanmış bir susturma bulunamadı',
        'view_all_bans' => 'Tüm Yasaklamaları Görüntüle',
        'view_all_mutes' => 'Tüm Susturmaları Görüntüle'
    ],
    
    'search' => [
        'title' => 'Oyuncu Arama',
        'placeholder' => 'Oyuncu adı veya UUID girin...',
        'help' => 'Oyuncu adına veya tam UUID\'ye göre arama yapabilirsiniz',
        'button' => 'Ara',
        'no_results' => 'Bu oyuncu için ceza bulunamadı',
        'error' => 'Arama sırasında bir hata oluştu',
        'network_error' => 'Bir ağ hatası oluştu. Lütfen tekrar deneyin.'
    ],
    
    'stats' => [
        'title' => 'Sunucu İstatistikleri',
        'active_bans' => 'Aktif Yasaklamalar',
        'active_mutes' => 'Aktif Susturmalar',
        'total_warnings' => 'Toplam Uyarılar',
        'total_kicks' => 'Toplam Atmalar',
        'total_of' => '/',
        'all_time' => 'tüm zamanlar',
        'most_banned_players' => 'En Çok Yasaklanan Oyuncular',
        'most_active_staff' => 'En Aktif Yetkililer',
        'top_ban_reasons' => 'En Yaygın Yasaklama Nedenleri',
        'recent_activity_overview' => 'Son Etkinliklere Genel Bakış',
        'activity_by_day' => 'Güne Göre Etkinlik',
        'cache_cleared' => 'İstatistik önbelleği başarıyla temizlendi',
        'cache_clear_failed' => 'İstatistik önbelleği temizlenemedi',
        'clear_cache' => 'Önbelleği Temizle',
        'last_24h' => 'Son 24 Saat',
        'last_7d' => 'Son 7 Gün',
        'last_30d' => 'Son 30 Gün'
    ],
    
    'table' => [
        'player' => 'Oyuncu',
        'reason' => 'Neden',
        'staff' => 'Yetkili',
        'date' => 'Tarih',
        'expires' => 'Bitiş Tarihi',
        'status' => 'Durum',
        'actions' => 'Eylemler',
        'type' => 'Tür',
        'view' => 'Görüntüle',
        'total' => 'Toplam',
        'active' => 'Aktif',
        'last_ban' => 'Son Yasaklama',
        'last_action' => 'Son Eylem',
        'server' => 'Sunucu',
    ],
    
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Pasif',
        'expired' => 'Süresi Doldu',
        'removed' => 'Kaldırıldı',
        'completed' => 'Tamamlandı',
        'removed_by' => 'Kaldıran'
    ],
    
    'punishment' => [
        'permanent' => 'Kalıcı',
        'expired' => 'Süresi Doldu'
    ],
    
    'punishments' => [
        'no_data' => 'Ceza bulunamadı',
        'no_data_desc' => 'Şu anda görüntülenecek bir ceza bulunmuyor'
    ],
    
    'detail' => [
        'duration' => 'Süre',
        'time_left' => 'Kalan Süre',
        'progress' => 'İlerleme',
        'removed_by' => 'Kaldıran',
        'removed_date' => 'Kaldırılma Tarihi',
        'flags' => 'İşaretler',
        'other_punishments' => 'Diğer Cezalar'
    ],
    
    'time' => [
        'days' => '{count} gün',
        'hours' => '{count} saat',
        'minutes' => '{count} dakika'
    ],
    
    'pagination' => [
        'label' => 'Sayfa Gezintisi',
        'previous' => 'Önceki',
        'next' => 'Sonraki',
        'page_info' => 'Sayfa {current} / {total}'
    ],
    
    'footer' => [
        'rights' => 'Tüm hakları saklıdır.',
        'powered_by' => 'Altyapı',
        'license' => 'Lisanslıdır'
    ],
    
    'error' => [
        'not_found' => 'Sayfa bulunamadı',
        'server_error' => 'Sunucu hatası oluştu',
        'invalid_request' => 'Geçersiz istek',
        'punishment_not_found' => 'İstenen ceza bulunamadı.',
        'loading_failed' => 'Ceza detayları yüklenemedi.'
    ],
    
    'protest' => [
        'title' => 'Yasaklama İtirazı',
        'description' => 'Yasaklamanızın bir hata sonucu verildiğini düşünüyorsanız, incelenmesi için bir itirazda bulunabilirsiniz.',
        'how_to_title' => 'Yasaklama İtirazı Nasıl Yapılır',
        'how_to_subtitle' => 'Yasağın kaldırılmasını istemek için bu adımları izleyin:',
        'step1_title' => '1. Bilgilerinizi Toplayın',
        'step1_desc' => 'Bir itiraz göndermeden önce şunlara sahip olduğunuzdan emin olun:',
        'step1_items' => [
            'Minecraft kullanıcı adınız',
            'Yasaklandığınız tarih ve saat',
            'Yasaklanma nedeniniz',
            'Durumunuzu destekleyen herhangi bir kanıt'
        ],
        'step2_title' => '2. İletişim Yöntemleri',
        'step2_desc' => 'Yasaklama itirazınızı aşağıdaki yöntemlerden biriyle gönderebilirsiniz:',
        'discord_title' => 'Discord (Önerilen)',
        'discord_desc' => 'Discord sunucumuza katılın ve #yasaklama-itirazları kanalında bir bilet oluşturun',
        'discord_button' => 'Discord\'a Katıl',
        'email_title' => 'E-posta',
        'email_desc' => 'İtirazınızı içeren detaylı bir e-postayı şu adrese gönderin:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Web sitemizin forumundaki Yasaklama İtirazları bölümünde yeni bir gönderi oluşturun.',
        'forum_button' => 'Forumu Ziyaret Et',
        'step3_title' => '3. Neleri Dahil Etmelisiniz',
        'step3_desc' => 'İtirazınız şunları içermelidir: Takma adınız - Ne oldu - Neden itiraz ediyorsunuz - Ne olmasını istiyorsunuz - Siteden ID (ör. ban&id=181) - İsteğe bağlı: Kanıt olarak ekran görüntüsü.',
        'step3_items' => [
            'Minecraft kullanıcı adınız',
            'Yasaklanmanın yaklaşık tarihi ve saati',
            'Sizi yasaklayan yetkili (biliyorsanız)',
            'Yasağın neden haksız olduğunu düşündüğünüze dair ayrıntılı bir açıklama',
            'Durumunuzu destekleyen herhangi bir ekran görüntüsü veya kanıt',
            'Ne olduğuna dair dürüst bir anlatım'
        ],
        'step4_title' => '4. İncelenmesini Bekleyin',
        'step4_desc' => 'Yetkili ekibimiz itirazınızı 48-72 saat içinde inceleyecektir. Lütfen sabırlı olun ve aynı yasaklama için birden fazla itirazda bulunmayın.',
        'guidelines_title' => 'Önemli Kurallar',
        'guidelines_items' => [
            'İtirazınızda dürüst ve saygılı olun',
            'Yalan söylemeyin veya yanlış bilgi vermeyin',
            'Spam yapmayın veya birden fazla itiraz göndermeyin',
            'Yetkili ekibinin nihai kararını kabul edin',
            'Yasaklamadan kaçmak kalıcı bir yasaklamayla sonuçlanacaktır'
        ],
        'warning_title' => 'Uyarı',
        'warning_desc' => 'Yanlış bilgi vermek veya yetkilileri aldatmaya çalışmak, itirazınızın reddedilmesine ve cezanızın uzatılmasına neden olabilir.',
        'form_not_available' => 'Doğrudan itiraz gönderimi şu anda mevcut değildir. Lütfen yukarıdaki iletişim yöntemlerinden birini kullanın.'
    ],
    
    'admin' => [
        'dashboard' => 'Yönetim Paneli',
        'login' => 'Yönetici Girişi',
        'logout' => 'Çıkış Yap',
        'password' => 'Şifre',
        'export_data' => 'Verileri Dışa Aktar',
        'export_desc' => 'Ceza verilerini çeşitli formatlarda dışa aktarın',
        'import_data' => 'Verileri İçe Aktar',
        'import_desc' => 'JSON veya XML dosyalarından ceza verilerini içe aktarın',
        'data_type' => 'Veri Türü',
        'all_punishments' => 'Tüm Cezalar',
        'select_file' => 'Dosya Seç',
        'import' => 'İçe Aktar',
        'settings' => 'Ayarlar',
        'show_player_uuid' => 'Oyuncu UUID\'sini Göster',
        'footer_site_name' => 'Altbilgi Site Adı',
        'footer_site_name_desc' => 'Altbilgi telif hakkı metninde görüntülenen site adı',
    ],
];