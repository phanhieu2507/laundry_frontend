<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function getReviewSummary($userId)
    {
        $summary = Review::where('user_id', $userId)
                         ->selectRaw('rating, COUNT(*) as count')
                         ->groupBy('rating')
                         ->get();

        return response()->json($summary);
    }

    public function getPendingReviews($userId)
    {
    
        $pendingReviews = Review::where('user_id', $userId)
                                ->where('status', 'pending')
                                ->with('service')
                                ->get();

        return response()->json($pendingReviews);
    }

    public function submitReview(Request $request, $reviewId)
{
    $validatedData = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string',
        'images.*' => 'image' // Kiểm tra mỗi file trong mảng phải là ảnh
    ]);

    $review = Review::find($reviewId);
    if (!$review) {
        return response()->json(['message' => 'Review not found'], 404);
    }

    $review->update([
        'rating' => $validatedData['rating'],
        'review' => $validatedData['review'],
        'status' => 'completed'
    ]);

    if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('public/review_images');
            $url = Storage::url($path);
            $review->images()->create(['image_path' => $url, 'review_id' => $review->review_id]);
        }
    }

    return response()->json(['message' => 'Review submitted successfully.']);
}


    

    public function getCompletedReviews($userId)
    {
        $completedReviews = Review::where('user_id', $userId)
                                  ->where('status', 'completed')
                                  ->get();

        return response()->json($completedReviews);
    }

    public function getReviewByService($serviceId)
    {
        $reviews = Review::where('service_id', $serviceId)
                                  ->where('status', 'completed')
                                  ->with('service')
                                  ->with('images')
                                  ->with('user')
                                  ->get();

        return response()->json($reviews);
    }

    public function getReviewStats($userId) {
        $pendingReviews = Review::where('user_id', $userId)
        ->where('status', 'pending')
        ->with('service') // Ensure your Services model has a relationship to
        ->get();

        $completedReviews = Review::where('user_id', $userId)
                                  ->where('status', 'completed')
                                  ->with('images') // Ensure your Review model has a relationship to images
                                  ->with('service') // Ensure your Services model has a relationship to
                                  ->get();
    
        $ratingsCount = $completedReviews->groupBy('rating')
                                         ->map(function ($items, $key) {
                                             return count($items);
                                         });
                                         
        $totalReviews = $completedReviews->count();
    
        return response()->json([
            'totalReviews' => $totalReviews,
            'ratingsCount' => $ratingsCount,
            'completedReviews' => $completedReviews, 
            'pendingReviews' => $pendingReviews
        ]);
    }
    

}
