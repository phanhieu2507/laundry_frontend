<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->get();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('user')->find($id);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
            'total_amount' => 'required|numeric',
            'payment_status' => 'required|string',
            'order_date' => 'required|date',
            'service' => 'required|string',
            'detail' => 'nullable|string',
            'promo_code' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Tìm user dựa trên số điện thoại
        $user = User::where('phone', $request->phone)->first();

        $promoCode = $request->promo_code;
        $discountAmount = 0;

        if ($promoCode) {
            $discountAmount = $this->calculateDiscount($request->discount_type, $request->discount, $request->total_amount);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $request->total_amount - $discountAmount,
            'payment_status' => $request->payment_status,
            'order_date' => $request->order_date,
            'service' => $request->service,
            'detail' => $request->detail,
            'promo_code' => $request->promo_code,
            'discount_amount' => $discountAmount
        ]);
        if ($order) {
    // Gửi thông báo
    $notification = new Notification([
        'user_id' => $user->id,
        'title' => 'Order Successful',
        'message' => 'Thank you for using our service. Your order has been placed successfully.'
    ]);
    $notification->save();

    // Gửi email
    Mail::to($user->email)->send(new OrderPlaced($order));
}
        $notification = new Notification([
            'user_id' => $user->id,
            'title' => 'Order Successful',
            'message' => 'Thank you for using our service. Your order has been placed successfully.'
        ]);
        $notification->save();

        $serviceNames = explode(',', $request->service);
$serviceNames = array_map('trim', $serviceNames); // Loại bỏ khoảng trắng thừa

$services = Service::whereIn('service_name', $serviceNames)->get();

foreach ($services as $service) {
    $review = new Review([
        'user_id' =>  $user->id,
        'order_id' => $order->order_id,
        'service_id' => $service->service_id,
        'status' => 'pending',
        'rating' => 0,
        'review' => null,
    ]);
    $review->save();
}
        return response()->json($order, 201);
    }

    protected function calculateDiscount( $discount_type, $discount, $totalAmount)
    {
        $discountValue = floatval($discount);

    if ($discount_type == 'percentage') {
        // Tính toán giảm giá dựa trên phần trăm
        return ($totalAmount * $discountValue) / 100;
    }

    // Tính toán giảm giá dựa trên giá trị cố định
    return min($discountValue, $totalAmount);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $request->validate([
            'user_id' => 'exists:users,id',
            'total_amount' => 'numeric',
            'payment_status' => 'in:paid,unpaid',
            'order_date' => 'date',
            'service' => 'string',
            'detail' => 'nullable|string',
        ]);

        $order->update($request->all());

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
    public function getUserRequests($id)
    {
        // Lấy danh sách yêu cầu của người dùng đăng nhập hiện tại
        $userRequests = Order::where('user_id', $id)->get();

        return response()->json($userRequests);
    }
}

