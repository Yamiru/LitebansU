<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => '用于查看服务器惩罚和封禁的公共界面'
    ],
    
    'nav' => [
        'home' => '首页',
        'bans' => '封禁列表',
        'mutes' => '禁言列表',
        'warnings' => '警告',
        'kicks' => '踢出',
        'statistics' => '统计',
        'language' => '语言',
        'theme' => '主题',
        'admin' => '管理',
        'protest' => '封禁申诉',
    ],
    
    'home' => [
        'welcome' => '服务器惩罚',
        'description' => '搜索玩家惩罚并查看最近的活动',
        'recent_activity' => '最近活动',
        'recent_bans' => '最近的封禁',
        'recent_mutes' => '最近的禁言',
        'no_recent_bans' => '未找到最近的封禁记录',
        'no_recent_mutes' => '未找到最近的禁言记录',
        'view_all_bans' => '查看所有封禁',
        'view_all_mutes' => '查看所有禁言'
    ],
    
    'search' => [
        'title' => '玩家搜索',
        'placeholder' => '输入玩家名称或UUID...',
        'help' => '您可以通过玩家名称或完整的UUID进行搜索',
        'button' => '搜索',
        'no_results' => '未找到该玩家的惩罚记录',
        'error' => '搜索时发生错误',
        'network_error' => '发生网络错误，请重试。'
    ],
    
    'stats' => [
        'title' => '服务器统计',
        'active_bans' => '当前有效的封禁',
        'active_mutes' => '当前有效的禁言',
        'total_warnings' => '总警告数',
        'total_kicks' => '总踢出数',
        'total_of' => '/',
        'all_time' => '总计',
        'most_banned_players' => '被封禁次数最多的玩家',
        'most_active_staff' => '最活跃的管理员',
        'top_ban_reasons' => '最常见的封禁原因',
        'recent_activity_overview' => '最近活动概览',
        'activity_by_day' => '每日活动',
        'cache_cleared' => '统计缓存已成功清除',
        'cache_clear_failed' => '清除统计缓存失败',
        'clear_cache' => '清除缓存',
        'last_24h' => '过去24小时',
        'last_7d' => '过去7天',
        'last_30d' => '过去30天'
    ],
    
    'table' => [
        'player' => '玩家',
        'reason' => '原因',
        'staff' => '执行者',
        'date' => '日期',
        'expires' => '到期时间',
        'status' => '状态',
        'actions' => '操作',
        'type' => '类型',
        'view' => '查看',
        'total' => '总计',
        'active' => '有效',
        'last_ban' => '最后一次封禁',
        'last_action' => '最后一次操作',
        'server' => '服务器',
    ],
    
    'status' => [
        'active' => '有效',
        'inactive' => '无效',
        'expired' => '已过期',
        'removed' => '已解除',
        'completed' => '已完成',
        'removed_by' => '解除者'
    ],
    
    'punishment' => [
        'permanent' => '永久',
        'expired' => '已过期'
    ],
    
    'punishments' => [
        'no_data' => '未找到惩罚记录',
        'no_data_desc' => '当前没有可显示的惩罚记录'
    ],
    
    'detail' => [
        'duration' => '持续时间',
        'time_left' => '剩余时间',
        'progress' => '进度',
        'removed_by' => '解除者',
        'removed_date' => '解除日期',
        'flags' => '标记',
        'other_punishments' => '其他惩罚'
    ],
    
    'time' => [
        'days' => '{count}天',
        'hours' => '{count}小时',
        'minutes' => '{count}分钟'
    ],
    
    'pagination' => [
        'label' => '页面导航',
        'previous' => '上一页',
        'next' => '下一页',
        'page_info' => '第 {current} 页 / 共 {total} 页'
    ],
    
    'footer' => [
        'rights' => '版权所有。',
        'powered_by' => '技术支持',
        'license' => '许可协议'
    ],
    
    'error' => [
        'not_found' => '页面未找到',
        'server_error' => '服务器发生错误',
        'invalid_request' => '无效请求',
        'punishment_not_found' => '无法找到请求的惩罚记录。',
        'loading_failed' => '加载惩罚详情失败。'
    ],
    
    'protest' => [
        'title' => '封禁申诉',
        'description' => '如果您认为您的封禁是错误的，您可以提交申诉以供审核。',
        'how_to_title' => '如何提交封禁申诉',
        'how_to_subtitle' => '请按照以下步骤申请解封：',
        'step1_title' => '1. 收集您的信息',
        'step1_desc' => '在提交申诉之前，请确保您拥有：',
        'step1_items' => [
            '您的Minecraft用户名',
            '您被封禁的日期和时间',
            '您被封禁的原因',
            '任何支持您申诉的证据'
        ],
        'step2_title' => '2. 联系方式',
        'step2_desc' => '您可以通过以下任一方式提交您的封禁申訴：',
        'discord_title' => 'Discord (推荐)',
        'discord_desc' => '加入我们的Discord服务器，并在 #ban-protests 频道中创建一个工单',
        'discord_button' => '加入Discord',
        'email_title' => '电子邮件',
        'email_desc' => '将包含您申诉详情的电子邮件发送至：',
        'forum_title' => '论坛',
        'forum_desc' => '在我们网站论坛的“封禁申诉”板块中创建一个新帖子。',
        'forum_button' => '访问论坛',
        'step3_title' => '3. 申诉内容',
        'step3_desc' => '您的申诉应包括：您的昵称 - 发生了什么 - 您申诉的原因 - 您希望的结果 - 网站上的ID（例如 ban&id=181） - 可选：截图作为证据。',
        'step3_items' => [
            '您的Minecraft用户名',
            '被封禁的大致日期和时间',
            '封禁您的管理员（如果知道）',
            '详细说明您认为封禁不公的原因',
            '任何支持您申诉的截图或证据',
            '对事件的诚实描述'
        ],
        'step4_title' => '4. 等待审核',
        'step4_desc' => '我们的管理团队将在48-72小时内审核您的申诉。请耐心等待，不要为同一次封禁提交多次申诉。',
        'guidelines_title' => '重要指南',
        'guidelines_items' => [
            '在申诉中请保持诚实和尊重',
            '不要说谎或提供虚假信息',
            '不要发送垃圾邮件或提交多次申诉',
            '接受管理团队的最终决定',
            '规避封禁将导致永久封禁'
        ],
        'warning_title' => '警告',
        'warning_desc' => '提交虚假信息或试图欺骗管理员将导致您的申诉被拒绝，并可能导致更长的惩罚。',
        'form_not_available' => '目前无法直接提交申诉。请使用上述联系方式之一。'
    ],
    
    'admin' => [
        'dashboard' => '管理后台',
        'login' => '管理员登录',
        'logout' => '登出',
        'password' => '密码',
        'export_data' => '导出数据',
        'export_desc' => '以各种格式导出惩罚数据',
        'import_data' => '导入数据',
        'import_desc' => '从JSON或XML文件导入惩罚数据',
        'data_type' => '数据类型',
        'all_punishments' => '所有惩罚',
        'select_file' => '选择文件',
        'import' => '导入',
        'settings' => '设置',
        'show_player_uuid' => '显示玩家UUID',
        'footer_site_name' => '页脚网站名称',
        'footer_site_name_desc' => '在页脚版权文本中显示的网站名称',
    ],
];