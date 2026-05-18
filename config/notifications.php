<?php

return [
    'categories' => [
        'like' => [
            'title'    => 'New Like',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'comment' => [
            'title'    => 'New Comment',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'follow' => [
            'title'    => 'New Follower',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'new_order' => [
            'title'    => 'New Order',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'payment_success' => [
            'title'    => 'Payment Successful',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'order_status' => [
            'title'    => 'Order Update',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'order_cancelled' => [
            'title'    => 'Order Cancelled',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'admin_action' => [
            'title'    => 'Account Update',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'chat_message' => [
            'title'    => 'New Message',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'custom' => [
            'title'    => 'Notification',
            'channels' => ['database', 'broadcast'],
            'push'     => false,
        ],
        'broadcast' => [
            'title'    => 'Announcement',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'post_flagged' => [
            'title'    => 'Post Update',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
        'welcome' => [
            'title'    => 'Welcome',
            'channels' => ['database', 'broadcast'],
            'push'     => true,
        ],
    ],

    'default_channels' => ['database', 'broadcast'],
];
