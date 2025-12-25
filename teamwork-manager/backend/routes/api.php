<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInviteController;
use App\Http\Controllers\GroupJoinRequestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\ContributionController;

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| PROTECTED API
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    /*
    |--------------------------------------------------------------------------
    | GROUP MANAGEMENT
    |--------------------------------------------------------------------------
    */

    // Danh sách nhóm user tham gia
    Route::get('/groups', [GroupController::class, 'index']);

    // Tạo nhóm
    Route::post('/groups', [GroupController::class, 'store']);

    // Xem chi tiết nhóm (member)
    Route::get('/groups/{group}', [GroupController::class, 'show'])
        ->middleware('group.role:member');

    // Chuyển quyền leader
    Route::post('/groups/{group}/transfer-leader', [GroupController::class, 'transferLeader'])
        ->middleware('group.role:leader');

    // Đóng nhóm (leader)
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])
        ->middleware('group.role:leader');

    /*
    |--------------------------------------------------------------------------
    | GROUP INVITE (OTP)
    |--------------------------------------------------------------------------
    */

    // Leader tạo OTP mời
    Route::post('/groups/{group}/invite', [GroupInviteController::class, 'generate'])
        ->middleware('group.role:leader');

    // Join group bằng OTP
    Route::post('/groups/join-by-otp', [GroupInviteController::class, 'join'])
        ->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | GROUP JOIN REQUEST
    |--------------------------------------------------------------------------
    */

    // Gửi request join group
    Route::post('/groups/{group}/join-request', [GroupJoinRequestController::class, 'store']);

    // Leader xem request
    Route::get('/groups/{group}/join-requests', [GroupJoinRequestController::class, 'index'])
        ->middleware('group.role:leader');

    // Leader approve / reject
    Route::post('/join-requests/{requestJoin}/approve', [GroupJoinRequestController::class, 'approve'])
        ->middleware('group.role:leader');

    Route::post('/join-requests/{requestJoin}/reject', [GroupJoinRequestController::class, 'reject'])
        ->middleware('group.role:leader');

    /*
    |--------------------------------------------------------------------------
    | TASKS (GROUP CONTEXT QUA group_id)
    |--------------------------------------------------------------------------
    */

    // Danh sách task theo group (member)
    Route::get('/tasks', [TaskController::class, 'index']);

    // Tạo task (leader)
    Route::post('/tasks', [TaskController::class, 'store'])
        ->middleware('group.role:leader');

    // Cập nhật task (leader)
    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->middleware('group.role:leader');

    // Huỷ task (leader)
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->middleware('group.role:leader');

    /*
    |--------------------------------------------------------------------------
    | SUBMISSIONS
    |--------------------------------------------------------------------------
    */

    // Nộp bài (user được giao task)
    Route::post('/tasks/{task}/submit', [SubmissionController::class, 'store'])
        ->middleware(['task.assigned', 'task.open']);

    /*
    |--------------------------------------------------------------------------
    | CHAT
    |--------------------------------------------------------------------------
    */

    // Xem tin nhắn (member)
    Route::get('/messages', [MessageController::class, 'index']);

    // Gửi tin nhắn (member)
    Route::post('/messages', [MessageController::class, 'store']);

    // Xoá tin nhắn (owner hoặc leader – controller check)
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    /*
    |--------------------------------------------------------------------------
    | REPORT
    |--------------------------------------------------------------------------
    */

    // Export báo cáo (leader)
    Route::get('/reports/export', [ReportController::class, 'export'])
        ->middleware('group.role:leader');

    /*
    |--------------------------------------------------------------------------
    | AI ASSISTANT
    |--------------------------------------------------------------------------
    */

    Route::get('/ai/dashboard', [AiAssistantController::class, 'dashboard']);

    /*
    |--------------------------------------------------------------------------
    | CONTRIBUTION / SCORE
    |--------------------------------------------------------------------------
    */

    // Chỉ leader xem
    Route::get('/contributions', [ContributionController::class, 'show'])
        ->middleware('group.role:leader');
});
