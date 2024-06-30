<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Review;
class DashboardController extends Controller
{
    // DashboardController

public function getDashboardStats()
{
    $totalServices = DB::table('services')->count();
    $totalCustomers = DB::table('users')->where('role', 'user')->count(); // Giả sử có trường role để phân biệt khách hàng
    $totalOrdersThisMonth = DB::table('orders')
                              ->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->count();
    $totalEarningsThisMonth = DB::table('orders')
                                 ->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->sum('total_amount');

    return response()->json([
        'totalServices' => $totalServices,
        'totalCustomers' => $totalCustomers,
        'totalOrdersThisMonth' => $totalOrdersThisMonth,
        'totalEarningsThisMonth' => $totalEarningsThisMonth
    ]);
}

public function getWeeklyEarnings()
{
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek = Carbon::now()->endOfWeek();

    $earnings = DB::table('orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->groupBy('date')
                ->get();

    return response()->json($earnings);
}
public function getTopUsers()
{
    $topUsers = User::withCount('orders')
                     ->with(['orders' => function($query) {
                         $query->selectRaw('user_id, SUM(total_amount) as total_spent')
                               ->groupBy('user_id');
                     }])
                     ->orderBy('orders_count', 'desc')
                     ->take(10)
                     ->get();

    return response()->json($topUsers);
}

public function getTopServices()
{
    // Lấy service_id, đếm số lượng reviews và lấy service_name từ bảng services
    $servicesWithReviewsCount = Review::join('services', 'reviews.service_id', '=', 'services.service_id')
                                      ->select('services.service_id as service_id', 'services.service_name as service_name')
                                      ->selectRaw('COUNT(reviews.review_id) as reviews_count')
                                      ->groupBy('services.service_id', 'services.service_name')
                                      ->orderBy('reviews_count', 'desc')
                                      ->get();

    // Lấy trung bình rating cho các reviews đã hoàn thành cho mỗi service
    $averageRatings = Review::select('service_id')
                            ->selectRaw('AVG(rating) as average_rating')
                            ->where('status', 'completed')
                            ->groupBy('service_id')
                            ->pluck('average_rating', 'service_id');

    // Kết hợp dữ liệu
    $topServices = $servicesWithReviewsCount->map(function ($item) use ($averageRatings) {
        return [
            'service_id' => $item->service_id,
            'service_name' => $item->service_name,
            'total_orders' => $item->reviews_count,
            'average_rating' => $averageRatings[$item->service_id] ?? 0 // Sử dụng 0 nếu không có dữ liệu rating
        ];
    });

    return response()->json($topServices);
}

public function getGeneralPromoCodeStatistics()
    {
        $totalCodes = DB::table('promo_codes')->count();
        $totalRevenue = DB::table('orders')
                          ->whereNotNull('promo_code')
                          ->sum('total_amount');
        $totalDiscount = DB::table('orders')
                           ->whereNotNull('promo_code')
                           ->sum('discount_amount');

        return response()->json([
            'totalPromoCodesIssued' => $totalCodes,
            'totalRevenueFromPromoCodes' => $totalRevenue,
            'totalDiscountFromPromoCodes' => $totalDiscount
        ]);
    }

    // API để thống kê từng mã giảm giá
    public function getDetailedPromoCodeStatistics()
    {
        $details = DB::table('promo_codes')
                     ->leftJoin('orders', 'promo_codes.code', '=', 'orders.promo_code')
                     ->select('promo_codes.code', DB::raw('COUNT(orders.order_id) as times_used'), 'promo_codes.usage_limit', DB::raw('SUM(orders.total_amount) as total_revenue'), DB::raw('SUM(orders.discount_amount) as total_discount'))
                     ->groupBy('promo_codes.code', 'promo_codes.usage_limit')
                     ->get();

        return response()->json(['promo_code_details' => $details]);
    }

}
