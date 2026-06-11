<?php

return [
    'masterPages' => [
        'notification' => 'Notification',
    ],
    'session' =>[
        'notification' => 'Your session has expired',
        'description' => 'Please reload the page to continue using the app.',
        'buttonReload'=> 'Reload'
    ],
    'blog' => [
        'successfulNotification' => 'Thank you for leaving a comment',
    ],
    'success' => [
        'register' => 'Registration successful !',
        'message' => 'Thank you for your registration.',
        'home' => 'BACK TO HOME',
    ],
    'invitation' =>[
        'code_error_message' => 'Code is incorrect. Please re-enter.',
        'password_error_message' => 'Password or code is incorrect. Please re-enter.',
    ],
    'conference' =>[
        'successMessage_create' => 'Created successfully',
        'successMessage_update' => 'Updated successfully',
        'successMessage_delete' => 'Deleted successfully',
        'errorMessage_create' => 'Created failed',
        'errorMessage_update' => 'Updated failed',
        'errorMessage_delete' => 'Deleted failed',
    ],
    'report' =>[
        'not_find' => "We couldn't find your data. Please fill in the information."
    ],
    'register' =>[
        'not_find' => "We couldn't find your data. Please fill in the information."
    ],
    'track' =>[
        'not_find' => 'The information you entered does not match our records. Please try again.'
    ],
    'registration_expired' => 'The registration period for this conference has ended. Please try again for the next conference.'
];
