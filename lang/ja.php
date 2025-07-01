<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'サーバーの処罰やBAN情報を閲覧するための公開インターフェース'
    ],
    
    'nav' => [
        'home' => 'ホーム',
        'bans' => 'BANリスト',
        'mutes' => 'ミュートリスト',
        'warnings' => '警告',
        'kicks' => 'キック',
        'statistics' => '統計',
        'language' => '言語',
        'theme' => 'テーマ',
        'admin' => '管理',
        'protest' => '異議申し立て',
    ],
    
    'home' => [
        'welcome' => 'サーバーの処罰一覧',
        'description' => 'プレイヤーの処罰を検索し、最近のアクティビティを確認します',
        'recent_activity' => '最近のアクティビティ',
        'recent_bans' => '最近のBAN',
        'recent_mutes' => '最近のミュート',
        'no_recent_bans' => '最近のBANは見つかりませんでした',
        'no_recent_mutes' => '最近のミュートは見つかりませんでした',
        'view_all_bans' => 'すべてのBANを表示',
        'view_all_mutes' => 'すべてのミュートを表示'
    ],
    
    'search' => [
        'title' => 'プレイヤー検索',
        'placeholder' => 'プレイヤー名またはUUIDを入力...',
        'help' => 'プレイヤー名または完全なUUIDで検索できます',
        'button' => '検索',
        'no_results' => 'このプレイヤーに対する処罰は見つかりませんでした',
        'error' => '検索中にエラーが発生しました',
        'network_error' => 'ネットワークエラーが発生しました。もう一度お試しください。'
    ],
    
    'stats' => [
        'title' => 'サーバー統計',
        'active_bans' => '有効なBAN',
        'active_mutes' => '有効なミュート',
        'total_warnings' => '警告の合計',
        'total_kicks' => 'キックの合計',
        'total_of' => '/',
        'all_time' => '全期間',
        'most_banned_players' => '最も多くBANされたプレイヤー',
        'most_active_staff' => '最もアクティブなスタッフ',
        'top_ban_reasons' => '主なBAN理由',
        'recent_activity_overview' => '最近のアクティビティ概要',
        'activity_by_day' => '日別アクティビティ',
        'cache_cleared' => '統計キャッシュが正常にクリアされました',
        'cache_clear_failed' => '統計キャッシュのクリアに失敗しました',
        'clear_cache' => 'キャッシュをクリア',
        'last_24h' => '過去24時間',
        'last_7d' => '過去7日間',
        'last_30d' => '過去30日間'
    ],
    
    'table' => [
        'player' => 'プレイヤー',
        'reason' => '理由',
        'staff' => '実行者',
        'date' => '日付',
        'expires' => '有効期限',
        'status' => 'ステータス',
        'actions' => '操作',
        'type' => '種類',
        'view' => '表示',
        'total' => '合計',
        'active' => '有効',
        'last_ban' => '最後のBAN',
        'last_action' => '最後の操作',
        'server' => 'サーバー',
    ],
    
    'status' => [
        'active' => '有効',
        'inactive' => '無効',
        'expired' => '期限切れ',
        'removed' => '解除済み',
        'completed' => '完了',
        'removed_by' => '解除者'
    ],
    
    'punishment' => [
        'permanent' => '無期限',
        'expired' => '期限切れ'
    ],
    
    'punishments' => [
        'no_data' => '処罰は見つかりませんでした',
        'no_data_desc' => '現在表示する処罰はありません'
    ],
    
    'detail' => [
        'duration' => '期間',
        'time_left' => '残り時間',
        'progress' => '進行状況',
        'removed_by' => '解除者',
        'removed_date' => '解除日',
        'flags' => 'フラグ',
        'other_punishments' => 'その他の処罰'
    ],
    
    'time' => [
        'days' => '{count}日',
        'hours' => '{count}時間',
        'minutes' => '{count}分'
    ],
    
    'pagination' => [
        'label' => 'ページナビゲーション',
        'previous' => '前へ',
        'next' => '次へ',
        'page_info' => 'ページ {current} / {total}'
    ],
    
    'footer' => [
        'rights' => 'All rights reserved.',
        'powered_by' => 'Powered by',
        'license' => 'Licensed under'
    ],
    
    'error' => [
        'not_found' => 'ページが見つかりません',
        'server_error' => 'サーバーエラーが発生しました',
        'invalid_request' => '無効なリクエスト',
        'punishment_not_found' => '要求された処罰は見つかりませんでした。',
        'loading_failed' => '処罰詳細の読み込みに失敗しました。'
    ],
    
    'protest' => [
        'title' => 'BANへの異議申し立て',
        'description' => 'あなたのBANが誤りだと思われる場合は、審査のために異議を申し立てることができます。',
        'how_to_title' => '異議申し立ての方法',
        'how_to_subtitle' => 'BAN解除を申請するには、次の手順に従ってください：',
        'step1_title' => '1. 情報を集める',
        'step1_desc' => '異議を申し立てる前に、次の情報があることを確認してください：',
        'step1_items' => [
            'あなたのMinecraftユーザー名',
            'BANされた日時',
            'BANの理由',
            'あなたの主張を裏付ける証拠'
        ],
        'step2_title' => '2. 連絡方法',
        'step2_desc' => '次のいずれかの方法でBANへの異議申し立てを提出できます：',
        'discord_title' => 'Discord (推奨)',
        'discord_desc' => '私たちのDiscordサーバーに参加し、#ban-protests チャンネルでチケットを作成してください',
        'discord_button' => 'Discordに参加',
        'email_title' => 'Eメール',
        'email_desc' => '異議申し立ての詳細を記載したEメールを送信してください：',
        'forum_title' => 'フォーラム',
        'forum_desc' => 'ウェブサイトのフォーラムの「BANへの異議申し立て」セクションに新しい投稿を作成してください。',
        'forum_button' => 'フォーラムへ',
        'step3_title' => '3. 記載事項',
        'step3_desc' => '異議申し立てには以下を含める必要があります：あなたのニックネーム - 何が起こったか - なぜ異議を申し立てるのか - どうしてほしいか - サイトからのID（例：ban&id=181） - オプション：証拠のスクリーンショット。',
        'step3_items' => [
            'あなたのMinecraftユーザー名',
            'BANされたおおよその日時',
            'あなたをBANしたスタッフ（分かれば）',
            'BANが不当だと思う理由の詳細な説明',
            'あなたの主張を裏付けるスクリーンショットや証拠',
            '何が起こったかの正直な説明'
        ],
        'step4_title' => '4. 審査を待つ',
        'step4_desc' => '私たちのスタッフチームが48〜72時間以内にあなたの異議申し立てを審査します。同じBANに対して複数の申し立てをしないで、辛抱強くお待ちください。',
        'guidelines_title' => '重要なガイドライン',
        'guidelines_items' => [
            '異議申し立ては正直かつ敬意をもって行ってください',
            '嘘をついたり、偽の情報を提供したりしないでください',
            'スパム行為や複数の申し立てをしないでください',
            'スタッフチームの最終決定を受け入れてください',
            'BAN回避は永久BANにつながります'
        ],
        'warning_title' => '警告',
        'warning_desc' => '偽情報の提出やスタッフを欺こうとする試みは、申し立ての却下につながり、処罰が延長される可能性があります。',
        'form_not_available' => '直接の異議申し立ては現在利用できません。上記の連絡方法のいずれかをご利用ください。'
    ],
    
    'admin' => [
        'dashboard' => '管理ダッシュボード',
        'login' => '管理者ログイン',
        'logout' => 'ログアウト',
        'password' => 'パスワード',
        'export_data' => 'データのエクスポート',
        'export_desc' => '様々な形式で処罰データをエクスポートします',
        'import_data' => 'データのインポート',
        'import_desc' => 'JSONまたはXMLファイルから処罰データをインポートします',
        'data_type' => 'データタイプ',
        'all_punishments' => 'すべての処罰',
        'select_file' => 'ファイルを選択',
        'import' => 'インポート',
        'settings' => '設定',
        'show_player_uuid' => 'プレイヤーUUIDを表示',
        'footer_site_name' => 'フッターのサイト名',
        'footer_site_name_desc' => 'フッターの著作権表示に表示されるサイト名',
    ],
];