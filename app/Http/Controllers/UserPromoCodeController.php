<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Models\Notification;
use App\Mail\PromoCodeGifted;
use Illuminate\Support\Facades\Mail;

class UserPromoCodeController extends Controller
{
    public function assignToUsers(Request $request, $promoCodeId)
    {
        $promoCode = PromoCode::find($promoCodeId);
        if (!$promoCode) {
            return response()->json(['message' => 'Promo code not found'], 404);
        }
    
        $userIds = $request->input('userIds');
        $quantity = $request->input('quantity'); // Số lượng mã giảm giá cho mỗi người dùng

        if (is_array($userIds) && count($userIds) > 0) {
            foreach ($userIds as $userId) {
                for ($i = 0; $i < $quantity; $i++) {
                    // Thêm mã giảm giá mới cho mỗi người dùng
                    UserPromoCode::create([
                        'promo_code_id' => $promoCodeId,
                        'user_id' => $userId,
                        'is_used' => false // Khởi tạo chưa sử dụng
                    ]);
                }
                $user = User::find($userId); // Tìm thông tin người dùng để gửi mail

                Mail::to($user->email)->send(new PromoCodeGifted($user, $quantity));
                // Tạo thông báo cho mỗi người dùng được tặng mã
                $notification = new Notification([
                    'user_id' => $userId,
                    'title' => "Promo Code Gift",
                    'message' => "You have been gifted $quantity promo code(s). Check your promo code section for more details."
                ]);
                $notification->save();
            }
            return response()->json(['message' => 'Promo code(s) assigned successfully to users']);
        }
    
        return response()->json(['message' => 'No users provided'], 400);
    }

    public function getAssignedUsers($promoCodeId)
    {
        $assignedUsers = UserPromoCode::where('promo_code_id', $promoCodeId)
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'phone');
            }])
            ->get(['user_id', 'created_at', 'is_used']);

        return response()->json($assignedUsers);
    }
}
