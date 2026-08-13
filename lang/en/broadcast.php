<?php

return [
    'label' => 'Broadcast',
    'navigation' => 'Broadcast',
    'all_users' => 'All users',
    'now' => 'Now',
    'open' => 'Open',

    'actions' => [
        'send' => 'Send',
        'resend' => 'Resend',
    ],

    'section' => [
        'message' => 'Message',
        'message_description' => 'What do you want to tell your users?',
        'audience' => 'Audience',
        'audience_description' => 'Choose who should receive this broadcast.',
        'details' => 'Details',
        'schedule' => 'Schedule',
        'schedule_description' => 'Send now, schedule it, or save it as a draft.',
    ],

    'form' => [
        'title' => 'Title',
        'roles' => 'Roles',
        'users' => 'Users',
        'recipients_helper' => 'Leave empty to send to all users.',
        'type' => 'Type',
        'channel' => 'Channel',
        'body' => 'Content',
        'url' => 'URL',
        'url_helper' => 'Optional. Adds an action button under the notification content.',
        'label' => 'Button label',
        'label_helper' => 'Optional. Text of the action button, e.g. "Read more".',
        'label_placeholder' => 'Read more',
        'save_as_draft' => 'Save as draft',
        'send_now' => 'Send now',
        'send_at' => 'Send at',
        'send_at_helper' => 'Pick a date to schedule the broadcast.',
    ],

    'type' => [
        'info' => 'Info',
        'warning' => 'Warning',
        'success' => 'Success',
        'error' => 'Error',
    ],

    'channel' => [
        'database' => 'Database',
        'mail' => 'Email',
    ],

    'column' => [
        'title' => 'Title',
        'type' => 'Type',
        'status' => 'Status',
        'channels' => 'Channel',
        'roles' => 'Roles',
        'users' => 'Users',
        'send_at' => 'Send at',
        'sent_at' => 'Sent at',
        'created_at' => 'Created at',
    ],

    'status' => [
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'sent' => 'Sent',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Broadcast sent',
            'body' => 'The broadcast is being processed and will be sent shortly.',
        ],
        'saved' => [
            'title' => 'Broadcast saved',
            'body' => 'Your broadcast has been saved and can be sent later.',
        ],
        'send' => [
            'title' => 'Broadcast sent',
            'body' => 'The broadcast has been sent to its recipients.',
        ],
        'resend' => [
            'title' => 'Broadcast resent',
            'body' => 'The broadcast has been resent with the updated data.',
        ],
    ],
];
