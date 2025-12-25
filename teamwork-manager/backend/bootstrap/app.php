<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        | Dùng cho route middleware
        | VD: ->middleware(['auth:sanctum', 'ensure.active', 'group.role:leader'])
        */

        $middleware->alias([

            /* ===============================
             * 🔐 USER / AUTH
             * =============================== */
            'ensure.active' => \App\Http\Middleware\EnsureUserIsActive::class,

            /* ===============================
             * 👥 GROUP CONTEXT / PERMISSION
             * =============================== */
            'group.context'    => \App\Http\Middleware\GroupContextMiddleware::class,
            'group.exists'     => \App\Http\Middleware\EnsureGroupNotDeleted::class,

            // Gộp leader + member
            'group.role'       => \App\Http\Middleware\EnsureGroupRole::class,

            // (Nếu bạn vẫn dùng middleware cũ riêng lẻ thì giữ)
            'group.member'     => \App\Http\Middleware\GroupMemberMiddleware::class,
            'group.leader'     => \App\Http\Middleware\GroupLeaderMiddleware::class,

            /* ===============================
             * 🧩 TASK
             * =============================== */
            'task.assigned'    => \App\Http\Middleware\EnsureTaskAssigned::class,
            'task.open'        => \App\Http\Middleware\EnsureTaskOpen::class,

            /* ===============================
             * 📤 SUBMISSION
             * =============================== */
            'submission.owner' => \App\Http\Middleware\EnsureSubmissionOwner::class,
            'submission.open'  => \App\Http\Middleware\EnsureSubmissionNotLate::class,

            /* ===============================
             * 🌐 API
             * =============================== */
            'api.json'         => \App\Http\Middleware\EnsureApiJson::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Có thể custom handler sau
    })
    ->create();
