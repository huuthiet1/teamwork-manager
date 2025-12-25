<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ]);

        // Có thể bọc transaction cho chắc (dù chỉ 1 bảng)
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'is_active'     => 1,
            'last_active_at'=> now(),
        ]);

        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'token'   => $token,
            'user'    => $this->safeUser($user),
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Sai email hoặc mật khẩu'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Check tài khoản bị vô hiệu hóa
        if (!$user->is_active || $user->locked_at) {
            Auth::logout();

            return response()->json([
                'message' => 'Tài khoản đã bị vô hiệu hóa'
            ], 403);
        }

        // Update last active
        $user->update([
            'last_active_at' => now(),
        ]);

        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->safeUser($user),
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Đã đăng xuất'
        ]);
    }

    /**
     * GET CURRENT USER
     */
    public function me(Request $request)
    {
        return response()->json(
            $this->safeUser($request->user())
        );
    }

    /**
     * CHANGE PASSWORD
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        $user->update([
            'password_hash' => Hash::make($request->new_password),
        ]);

        // Revoke toàn bộ token → bắt đăng nhập lại
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công, vui lòng đăng nhập lại'
        ]);
    }

    /**
     * Ẩn field nhạy cảm
     */
    private function safeUser(User $user)
    {
        return $user->makeHidden([
            'password_hash',
            'locked_by',
            'locked_at',
            'lock_reason',
        ]);
    }
}
