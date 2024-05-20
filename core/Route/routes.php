<?php
return [
    /*
     * Pattern:
     * [HTTP Method, path, controller name, controller method, middleware access (all/guest/auth)]
     * Paths MUST BE in snake_case! e.g. /page/some_action, otherwise you may get a 404 error.
     */
    ['GET',  '/',                                   'Home',          'index',                  'all'],
    
    ['GET',  '/b',                                  'Board',         'index',                  'all'],

    ['GET',  '/dashboard',                          'Dashboard',     'index',                  'auth'],
    ['POST', '/dashboard/add',                      'Dashboard',     'addBookmark',            'auth'],
    ['POST', '/dashboard/change_bookmarks_privacy', 'Dashboard',     'changeBookmarksPrivacy', 'auth'],
    ['POST', '/dashboard/delete',                   'Dashboard',     'deleteBookmark',         'auth'],
    ['POST', '/dashboard/edit',                     'Dashboard',     'editBookmark',           'auth'],
    ['GET',  '/dashboard/get_bookmark',             'Dashboard',     'getBookmark',            'auth'],
    
    ['GET',  '/password_reset',                     'PasswordReset', 'index',                  'guest'],
    ['POST', '/password_reset',                     'PasswordReset', 'sendEmail',              'guest'],
    ['GET',  '/password_reset/email_sent',          'PasswordReset', 'emailSent',              'guest'],
    ['GET',  '/password_reset/reset',               'PasswordReset', 'resetIndex',             'guest'],
    ['POST', '/password_reset/reset',               'PasswordReset', 'reset',                  'guest'],
    ['GET',  '/password_reset/success',             'PasswordReset', 'success',                'guest'],

    ['GET',  '/settings',                           'Settings',      'index',                  'auth'],
    ['POST', '/settings/change_email',              'Settings',      'changeEmail',            'auth'],
    ['POST', '/settings/change_password',           'Settings',      'changePassword',         'auth'],
    ['POST', '/settings/delete_account',            'Settings',      'deleteAccount',          'auth'],
    
    ['GET',  '/signin',                             'SignIn',        'index',                  'guest'],
    ['POST', '/signin',                             'SignIn',        'signIn',                 'guest'],

    ['GET',  '/signout',                            'SignOut',       'index',                  'auth'],
    
    ['GET',  '/signup',                             'SignUp',        'index',                  'guest'],
    ['POST', '/signup',                             'SignUp',        'signUp',                 'guest']
];