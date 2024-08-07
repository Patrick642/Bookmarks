<?php
return [
    /*
    Paths MUST BE written in lower case!
    [HTTP Method, path, controller name, controller method, middleware access (all/guest/auth), XMLHttpRequest (default=false)]
    */
    ['GET',  '/',                                   'Home',          'index',                         'all',   false],
    
    ['GET',  '/account_delete',                     'AccountDelete', 'index',                         'auth',  false],
    ['POST', '/account_delete',                     'AccountDelete', 'delete',                        'auth',  false],
    
    ['GET',  '/shared',                             'Bookmarks',     'shared',                        'all',   false],
    
    ['GET',  '/dashboard',                          'Bookmarks',     'dashboard',                     'auth',  false],
    ['POST', '/dashboard/add',                      'Bookmarks',     'addBookmark',                   'auth',  true],
    ['POST', '/dashboard/change_bookmarks_privacy', 'Bookmarks',     'changeBookmarksPrivacy',        'auth',  true],
    ['POST', '/dashboard/delete',                   'Bookmarks',     'deleteBookmark',                'auth',  true],
    ['POST', '/dashboard/edit',                     'Bookmarks',     'editBookmark',                  'auth',  true],
    ['GET',  '/dashboard/get_bookmark',             'Bookmarks',     'getBookmark',                   'auth',  true],
       
    ['GET',  '/get_more_bookmarks',                 'Bookmarks',     'getMoreBookmarks',              'all',   true],
    
    ['GET',  '/settings',                           'Settings',      'index',                         'auth',  false],
    ['POST', '/settings/change_password',           'Settings',      'changePassword',                'auth',  true],
    
    ['GET',  '/signin',                             'SignIn',        'index',                         'guest', false],
    ['POST', '/signin',                             'SignIn',        'signIn',                        'guest', false],
    
    ['GET',  '/signout',                            'SignOut',       'index',                         'auth',  false],
    
    ['GET',  '/signup',                             'SignUp',        'index',                         'guest', false],
    ['POST', '/signup',                             'SignUp',        'signUp',                        'guest', false]
];