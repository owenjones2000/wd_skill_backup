<?php

return [
    'bingoforcash' => [
        'name' => 'bingoforcash',
        'host' => env('BINGO_DB_HOST'),
        'user' => env('BINGO_DB_USER'),
        'password' => env('BINGO_DB_PASSWORD'),
        'database' => env('BINGO_DB_DATABASE', 'bingocash'),
        'table' => [
            "activities",
            "black_list_users",
            "cash_apply",
            "charge_configs",
            "charge_mails",
            "charge_methods",
            "configs",
            "coupon_records",
            // "eccpay_charges",
            "email_templates",
            "fomo_charges",
            "forbidden_area",
            "game_params",
            "game_seeds",
            "gc_charges",
            "gc_refund_records",
            "infinite_matches",
            "levels",
            "match_rules",
            "paypal_proxy_charges",
            "pic",
            "push_message_configs",
            "scenes",
            "seeds",
            "small_games",
            "thinking_analytics",
            "user",
            // "user_activities", 
            "user_balance",
            "user_balance_capacity",
            "user_charges",
            "user_comeback",   
            "user_devices",
            "user_email",
            "user_email_records",
            "user_game_capacity",
            "user_guide",
            // "user_incomes",
            "user_infinite_matches",
            "user_info",
            "user_match_capacity",
            "user_match_rule_statistics",
            "user_match_statistics",
            "user_params",
            "user_pay_refund_statistics",
            "user_prize_records",
            "user_ranklist",
            "user_robot_ref",
            "user_scenes",
            "user_small_games",
            "user_source",
            "user_suggests",
            "user_xp",
            "white_list_users",
        ],
        'skip'  => [
            'eccpay_charges',
            'gc_charges',
            'paypal_proxy_charges',
            'user_balance_capacity',
            'user_email_records',
            'user_game_capacity',
            'user_match_capacity',
            'user_match_rule_statistics',
            'user_match_statistics',
            'user_prize_records',
            'user_ranklist',
            'user_small_games',
        ],
        'dir' => 'bingoforcash/db/',
        'sub_table' => false,
    ],
    'bingogo' => [
        'name' => 'bingogo',
        'host' => env('BINGOGO_DB_HOST'),
        'user' => env('BINGOGO_DB_USER'),
        'password' => env('BINGOGO_DB_PASSWORD'),
        'database' => env('BINGOGO_DB_DATABASE', 'bingocash'),
        'table' => [
            "activities",
            "black_list_users",
            "cash_apply",
            "charge_configs",
            "charge_mails",
            "charge_methods",
            "configs",
            "coupon_records",
            // "eccpay_charges",
            "email_templates",
            "fomo_charges",
            "forbidden_area",
            "game_params",
            "game_seeds",
            "gc_charges",
            "gc_refund_records",
            "infinite_matches",
            "levels",
            "match_rules",
            "paypal_proxy_charges",
            "pic",
            "push_message_configs",
            "scenes",
            "seeds",
            "small_games",
            "thinking_analytics",
            "user",
            // "user_activities", 
            "user_balance",
            "user_balance_capacity",
            "user_charges",
            "user_comeback",   
            "user_devices",
            "user_email",
            "user_email_records",
            "user_game_capacity",
            "user_guide",
            // "user_incomes",
            "user_infinite_matches",
            "user_info",
            "user_match_capacity",
            "user_match_rule_statistics",
            "user_match_statistics",
            "user_params",
            "user_pay_refund_statistics",
            "user_prize_records",
            "user_ranklist",
            "user_robot_ref",
            "user_scenes",
            "user_small_games",
            "user_source",
            "user_suggests",
            "user_xp",
            "white_list_users",
        ],
        'skip'  => [
            'eccpay_charges',
            'gc_charges',
            'paypal_proxy_charges',
            'user_balance_capacity',
            'user_email_records',
            'user_game_capacity',
            'user_match_capacity',
            'user_match_rule_statistics',
            'user_match_statistics',
            'user_prize_records',
            'user_ranklist',
            'user_small_games',
        ],
        'dir' => 'bingogo/db/',
        'sub_table' => false,
    ],
    'solitairearena' => [
        'name' => 'solitairearena',
        'host' => env('SOLITAIRE_DB_HOST'),
        'user' => env('SOLITAIRE_DB_USER'),
        'password' => env('SOLITAIRE_DB_PASSWORD'),
        'database' => env('SOLITAIRE_DB_DATABASE', 'solitaire'),
        'table' => [
            'users',
            'users_balance',
            'users_auth',
            'users_charge',
            'users_coins',
            'users_email',
            'users_info',
            'abtest_user_id',
            'x_users_param_4',
            'w4_users_ext_info',
            'w4_users_head_portrait',
            'w4_users_level',
            'x_users_total_4',
            'devices',
            'abtest_configs',
            'charge_configs',
            'email_message_configs',
            'global_configs',
            'platform_app_configs',
            'platform_feature_configs',
            'push_message_configs',
            'random_item_configs',
            'room_configs',
            'tickets_cash_configs',
            'w4_level_configs',
            'w4_seeds_v6',
            'words',
            'medals_rank_activities',
            'w4_new_charge_normal_group',
            'w4_new_charge_activity',
            'w4_new_charge_entry_group',
            'w4_new_charge_routine_group',
            'w4_new_charge_operate_group',
            'infinite_multi_game',
            'pay_config',
            'w4_seeds_easy',
            'user_league_rank',
            'league_rank',
            'user_league_rank_history',
            'league_rank_upgrade',
            'league_rank_config',  
        ],
        'dir' => 'solitairearena/db/',
        'sub_table' => false,
    ],
    'bingowinner' => [
        'name' => 'bingowinner',
        'host' => env('BINGOWINNER_DB_HOST'),
        'user' => env('BINGOWINNER_DB_USER'),
        'password' => env('BINGOWINNER_DB_PASSWORD'),
        'database' => env('BINGOWINNER_DB_DATABASE', 'chebingo'),
        'table' => [
            'abtest_configs',
            'abtest_user_id',
            'charge_configs',
            'devices',
            'email_message_configs',
            'global_configs',
            'infinite_multi_game',
            'league_rank',
            'league_rank_config',
            'league_rank_upgrade',
            'medals_rank_activities',
            'pay_config',
            'platform_app_configs',
            'platform_feature_configs',
            'push_message_configs',
            'random_item_configs',
            'room_configs',
            'tickets_cash_configs',
            'user_league_rank',
            'user_league_rank_history',
            'users',
            'users_auth',
            'users_balance',
            'users_charge',
            'users_coins',
            'users_email',
            'users_info',
            'w4_level_configs',
            'w4_new_charge_activity',
            'w4_new_charge_entry_group',
            'w4_new_charge_normal_group',
            'w4_new_charge_operate_group',
            'w4_new_charge_routine_group',
            'w4_seeds_easy',
            'w4_seeds_v6',
            'w4_users_ext_info',
            'w4_users_head_portrait',
            'w4_users_level',
            'words',
            'x_users_param_4',
            'x_users_total_4',
        ],
        'dir' => 'bingowinner/db/',
        'sub_table' => false,
    ],
    'test' => [
        'name' => 'test',
        'host' => env('TEST_DB_HOST'),
        'user' => env('TEST_DB_USER'),
        'password' => env('TEST_DB_PASSWORD'),
        'database' => env('TEST_DB_DATABASE', 'zxbingo'),
        'table' => ['devices', 'users'],
        'dir' => 'test/db/',
        'sub_table' => false,
    ],
    // 'import' => [
    //     'host' => '127.0.0.1',
    //     'user' => env('IMPORT_DB_USER', 'root'),
    //     'password' => env('IMPORT_DB_PASSWORD', 'fd2f909a7c34a235'),
    // ],
    // 'database' => [

    // ],

];
