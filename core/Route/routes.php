<?php
return [
    /*
     * Pattern:
     * [HTTP Method, path, controller, controller method, middleware access]
     * Paths MUST BE in snake_case! e.g. /page/some_action, otherwise you may get a 404 error.
     */
    ['GET', '/', 'Home', 'index', ''],

    ['GET', '/b', 'Board', 'index', ''],

    ['GET', '/settings', 'Settings', 'index', 'auth'],
    ['POST', '/settings/change_email', 'Settings', 'changeEmail', 'auth'],
    ['POST', '/settings/change_password', 'Settings', 'changePassword', 'auth'],
    ['POST', '/settings/delete_account', 'Settings', 'deleteAccount', 'auth'],

    ['GET', '/dashboard', 'Dashboard', 'index', 'auth'],
    ['GET', '/dashboard/get_bookmark', 'Dashboard', 'getBookmark', 'auth'],
    ['POST', '/dashboard/add', 'Dashboard', 'addBookmark', 'auth'],
    ['POST', '/dashboard/change_bookmarks_privacy', 'Dashboard', 'changeBookmarksPrivacy', 'auth'],
    ['POST', '/dashboard/delete', 'Dashboard', 'deleteBookmark', 'auth'],
    ['POST', '/dashboard/edit', 'Dashboard', 'editBookmark', 'auth'],

    ['GET', '/signin', 'SignIn', 'index', 'guest'],
    ['POST', '/signin', 'SignIn', 'signIn', 'guest'],

    ['GET', '/signup', 'SignUp', 'index', 'guest'],
    ['POST', '/signup', 'SignUp', 'signUp', 'guest'],

    ['GET', '/signout', 'SignOut', 'index', 'auth']
];