<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "user/head_cut",
        "user/chat_room_list",
        "admin/menu_switch",
        "admin/article_switch",
        "admin/delete_menu",
        "admin/delete_admin/*",
        "admin/delete_article",
        "admin/delete_class",
        "admin/delete_chatroom"
    ];
}
