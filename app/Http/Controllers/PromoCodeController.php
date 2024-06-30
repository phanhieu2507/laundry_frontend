<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use App\Models\User;
use App\Models\UserPromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoCodeController extends Controller
{
     // Lấy danh sách mã giảm giá
     public function index() {
        $promoCodes = PromoCode::all();
        return response()->json($promoCodes);
    }

    public function show($id)
    {
        $promoCode = PromoCode::find($id);
        return response()->json($promoCode);
    }

    // Tạo mã giảm giá mới
    public function store(Request $request) {
        $promoCode = PromoCode::create($request->all());
        return response()->json($promoCode, 201);
    }

    // Cập nhật mã giảm giá
    public function update(Request $request, PromoCode $promoCode) {
        $promoCode->update($request->all());
        return response()->json($promoCode);
    }

    // Xóa mã giảm giá
    public function destroy(PromoCode $promoCode) {
        $promoCode->delete();
        return response()->json(null, 204);
    }

    public function apply(Request $request) {
        // Tìm mã giảm giá theo code
        if($request->code == "") {
            return response()->json([
                'discount_type' => 'fixed',
                'discount_value' => 0
            ]);
        }
        $promoCode = PromoCode::where('code', $request->code)->first();
        $user = User::where('phone', $request->phone)->first();

        // Kiểm tra mã giảm giá có tồn tại không
        if (!$promoCode) {
            return response()->json(['message' => 'Promo code does not exist.'], 404);
        }
    
        // Kiểm tra các điều kiện của mã giảm giá
        if ($promoCode->status !== 'active') {
            return response()->json(['message' => 'Promo code is not active.'], 403);
        }
    
        if ($promoCode->valid_from > now() || $promoCode->valid_until < now()) {
            return response()->json(['message' => 'Promo code is expired.'], 403);
        }
    
        // Kiểm tra liệu mã đã được sử dụng quá giới hạn chưa
        if ($promoCode->times_used >= $promoCode->usage_limit) {
            return response()->json(['message' => 'This promo code has reached its usage limit.'], 410);
        }
    
        // Kiểm tra xem mã có được gán cho người dùng này không
        $userPromoCodeAssigned = UserPromoCode::where('promo_code_id', $promoCode->id)
                                              ->where('user_id', $user->id)
                                              ->exists();
    
        if (!$userPromoCodeAssigned) {
            return response()->json(['message' => 'Promo code is not assigned to you.'], 403);
        }
    
        // Kiểm tra mã đã được sử dụng chưa
        $userPromoCode = UserPromoCode::where('promo_code_id', $promoCode->id)
                                      ->where('user_id', $user->id)
                                      ->where('is_used', false)
                                      ->first();
    
        if (!$userPromoCode) {
            return response()->json(['message' => 'Promo code already used.'], 403);
        }
    
        // Cập nhật trạng thái sử dụng của mã giảm giá
        $userPromoCode->update(['is_used' => true]);
    
        // Trả về chi tiết giảm giá
        return response()->json([
            'discount_type' => $promoCode->discount_type,
            'discount_value' => $promoCode->discount_value
        ]);
    }

    public function getUserPromoCodes($userId)
    {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $promoCodes = DB::table('user_promo_codes')
                        ->join('promo_codes', 'user_promo_codes.promo_code_id', '=', 'promo_codes.id')
                        ->where('user_promo_codes.user_id', $userId)
                        ->select(
                            'promo_codes.id', 'promo_codes.code', 'promo_codes.description', 
                            'promo_codes.discount_type', 'promo_codes.discount_value', 
                            'promo_codes.valid_from', 'promo_codes.valid_until', 'promo_codes.status',
                            'user_promo_codes.is_used', 'user_promo_codes.created_at as assigned_on'
                        )
                        ->get()
                        ->map(function ($promoCode) {
                            return [
                                'id' => $promoCode->id,
                                'code' => $promoCode->code,
                                'description' => $promoCode->description,
                                'discount_type' => $promoCode->discount_type,
                                'discount_value' => $promoCode->discount_value,
                                'valid_from' => $promoCode->valid_from,
                                'valid_until' => $promoCode->valid_until,
                                'status' => $promoCode->status,
                                'is_used' => $promoCode->is_used,
                                'assigned_on' => $promoCode->assigned_on,
                            ];
                        });
    
        return response()->json($promoCodes);
    }
    
    
    
}
