<?php

namespace App\Http\Controllers;

use App\Models\RequestOrder;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\StatusUpdated;
use Illuminate\Support\Facades\Mail;

class RequestOrderController extends Controller
{
    public function index(Request $request) {
        $query = RequestOrder::query();
    
        // Logging incoming request parameters
        Log::emergency('Received request with parameters:', $request->all());
    
        // Filtering by today
        if ($request->time_filter === 'today') {
            $query->whereDate('created_at', '=', now()->toDateString());
            Log::emergency('Filtering by today', ['date' => now()->toDateString()]);
        }
    
        // Filtering by this week
        if ($request->time_filter === 'week') {
            $startOfWeek = now()->startOfWeek()->toDateTimeString();
            $endOfWeek = now()->endOfWeek()->toDateTimeString();
            $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        
        }
    
        // Filtering by this month
        if ($request->time_filter === 'month') {
            $startOfMonth = now()->startOfMonth()->toDateTimeString();
            $endOfMonth = now()->endOfMonth()->toDateTimeString();
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
          
        }
    
        // Additional status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
            Log::emergency('Filtering by status', ['status' => $request->status]);
        }
    
        $requestOrders = $query->with('user')->get();
    
        return response()->json($requestOrders);
    }
    
    
    

    public function show($id)
    {
        // Lấy thông tin của một request order cụ thể
        $requestOrder = RequestOrder::findOrFail($id);
        return response()->json($requestOrder);
    }

    public function store(Request $request)
    {
        // Lưu một request order mới
        $requestOrder = RequestOrder::create($request->all());
        return response()->json($requestOrder, 201);
    }

    public function update(Request $request, $id)
    {
        // Cập nhật thông tin của một request order
        $requestOrder = RequestOrder::findOrFail($id);
        $requestOrder->update($request->all());
        return response()->json($requestOrder, 200);
    }

    public function destroy($id)
    {
        // Xóa một request order
        $requestOrder = RequestOrder::findOrFail($id);
        $requestOrder->delete();
        return response()->json(null, 204);
    }

    public function adminIndex(Request $request)
{
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        // Lấy tất cả các request orders cùng với thông tin user liên quan và sắp xếp từ mới nhất
        $requests = RequestOrder::with('user')->orderBy('created_at', 'desc')->get();
    } else {
        // Lọc các request orders theo status cùng với thông tin user liên quan và sắp xếp từ mới nhất
        $requests = RequestOrder::with('user')->where('status', $status)->orderBy('created_at', 'desc')->get();
    }
    
    return response()->json($requests);
}

    

    public function updateStatus($id, $status)
{
    $requestOrder = RequestOrder::find($id);

    if (!$requestOrder) {
        return response()->json(['error' => 'Request not found'], 404);
    }

    $requestOrder->status = $status;
    $requestOrder->save();

    // Tạo và lưu thông báo
    $notification = new Notification([
        'user_id' => $requestOrder->user_id,
        'title' => "Request Update",
        'message' => "Your request $id has been updated to $status."
    ]);
    $notification->save();

    // Gửi email
    Mail::to($requestOrder->user->email)->send(new StatusUpdated($requestOrder));

    return response()->json(['message' => 'Status updated successfully']);
}
    

    public function getUserRequests($id)
    {
        // Lấy danh sách yêu cầu của người dùng đăng nhập hiện tại
        $userRequests = RequestOrder::where('user_id', $id)->get();

        return response()->json($userRequests);
    }
}
