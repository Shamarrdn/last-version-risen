<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featuredProducts = Product::with(['category', 'images', 'colors'])
            ->where('is_available', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $newProducts = Product::with(['category', 'images'])
            ->where('is_available', true)
            ->latest()
            ->take(4)
            ->get();

        foreach ($featuredProducts as $product) {
            if ($product->colors->isNotEmpty()) {
                $product->colors->transform(function ($color) {
                    if (!isset($color->color_code) || empty($color->color_code)) {
                        $color->color_code = $color->color ?? '#000000';
                    }
                    return $color;
                });
            }
        }

        $allCoupons = Coupon::where('is_active', 1)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc')
            ->get();

        $currentPage = $request->get('page', 1);
        $perPage = 2;
        $coupons = $allCoupons->forPage($currentPage, $perPage);
        $totalPages = ceil($allCoupons->count() / $perPage);

        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(3)
            ->get();

        $topCategories->transform(function ($category) {
            $category->image_url = $category->image ? Storage::url($category->image) : 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80';
            return $category;
        });

        $allProducts = Product::with(['images', 'colors'])
            ->where('is_available', true)
            ->get();

        $discountedProducts = Product::with(['category', 'images'])
            ->where('is_available', true)
            ->get()
            ->filter(function ($product) {
                return $product->hasDiscounts();
            })
            ->take(4);

        return view('index', compact(
            'featuredProducts',
            'newProducts',
            'discountedProducts',
            'allCoupons',
            'coupons',
            'currentPage',
            'totalPages',
            'topCategories',
            'allProducts'
        ));
    }
}
