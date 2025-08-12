@php
use Illuminate\Support\Facades\Storage;
@endphp

@props(['product'])

<div class="product-card">
    <div class="relative group">
        <div class="product-image mb-2 overflow-hidden rounded-lg bg-gray-100 relative">
            <a href="{{ route('products.show', $product->slug) }}">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    class="w-full h-64 object-cover transition duration-300 group-hover:scale-105">
            </a>
            @if(isset($product->stock) && $product->stock <= 0)
                <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                    نفذت الكمية
                </div>
            @endif
        </div>

        <div class="product-info px-1">
            <div class="product-categories flex flex-wrap gap-1 mb-1">
                @if(isset($product->category) && $product->category)
                    <span class="primary-category text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">
                        {{ $product->category }}
                    </span>
                @endif

                @if(isset($product->categories) && $product->categories)
                    @foreach($product->categories as $category)
                        <span class="secondary-category text-xs px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">
                            {{ $category['name'] }}
                        </span>
                    @endforeach
                @endif
            </div>

            <h3 class="product-title text-sm font-medium mb-1 transition-colors group-hover:text-blue-600">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <div class="product-price font-semibold text-blue-900 mb-1 flex items-center">
                @if(isset($product->price))
                    <span>{{ number_format($product->price, 2) }} ر.س</span>
                @elseif(isset($product->min_price) && isset($product->max_price))
                    @if($product->min_price == $product->max_price)
                        <span>{{ number_format($product->min_price, 2) }} ر.س</span>
                    @else
                        <span>{{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ر.س</span>
                    @endif
                @elseif(isset($product->price_range))
                    @if($product->price_range['min'] == $product->price_range['max'])
                        <span>{{ number_format($product->price_range['min'], 2) }} ر.س</span>
                    @else
                        <span>{{ number_format($product->price_range['min'], 2) }} - {{ number_format($product->price_range['max'], 2) }} ر.س</span>
                    @endif
                @endif
            </div>

            <div class="product-action flex justify-between items-center">
                <div class="product-rating flex items-center">
                    <span class="text-yellow-400 text-xs">★★★★★</span>
                    <span class="text-xs text-gray-500 mr-1">({{ $product->reviews ?? 0 }})</span>
                </div>

                <a href="{{ route('products.show', $product->slug) }}"
                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-800 px-2 py-1 rounded transition">
                    عرض التفاصيل
                </a>
            </div>
        </div>
    </div>
</div>
