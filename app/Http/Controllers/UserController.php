<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return response()->json($user, 200);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'status' => 'error',
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $user,
            'token' => $token
        ],
        'message' => 'Login successful'
    ]);
}

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'fail',
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address;
    $user->password = Hash::make($request->password);
    $user->save();

    // Gửi email xác thực
    $user->notify(new VerifyEmail()); // Đảm bảo rằng bạn đã tạo Notification này

    return response()->json([
        'status' => 'success',
        'message' => 'Registration successful. Please check your email to verify.',
        'user' => $user
    ]);
}

public function logout(Request $request)
{
    // Thu hồi token của người dùng hiện tại
    $request->user()->tokens()->delete();

    // Trả về phản hồi thành công
    return response()->json(['message' => 'Successfully logged out']);
}

public function verify(Request $request)
{
    $user = User::findOrFail($request->id); // Tìm người dùng dựa vào ID

    if (! $request->hasValidSignature()) {
        return response()->json(["message" => "Invalid or expired link"], 401);
    }

    $user->email_verified_at = now();
    $user->save();

    return response()->json(["message" => "Email verified successfully"]);
}

public function changePassword(Request $request)
{
    $user = User::where('id', $request->userId)->first();; // Lấy thông tin người dùng đăng nhập hiện tại

    $request->validate([
        'oldPassword' => 'required',
        'newPassword' => 'required|string|min:8',
    ]);

    // Kiểm tra mật khẩu cũ có chính xác không
    if (!Hash::check($request->oldPassword, $user->password)) {
        return response()->json(['error' => 'The old password does not match our records.'], 401);
    }

    // Cập nhật mật khẩu mới
    $user->password = Hash::make($request->newPassword);
    $user->save();

    return response()->json(['message' => 'Password updated successfully']);
}


}
