@extends('admin.layouts.app')
@section('title', 'Product Details')

@section('head')
    <!-- Slick CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
@endsection

@section('content')
<div class="container mx-auto px-6 py-8" x-data="{ showReject: false }">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="lg:flex">
            <!-- Slider Section -->
            <div class="lg:w-2/5 w-full">
                <!-- Main Product Slider -->
                <div class="main-slider">
                    @if($product->cover_image)
                        <div>
                            <img src="{{ asset('storage/' . $product->cover_image) }}"
                                 alt="Cover Image"
                                 class="w-full h-96 object-cover rounded-t-lg lg:rounded-none" />
                        </div>
                    @endif
                    @foreach($product->images as $image)
                        <div>
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Product Image"
                                 class="w-full h-96 object-cover" />
                        </div>
                    @endforeach
                </div>

                <!-- Thumbnail Slider -->
                <div class="thumb-slider mt-4 hidden lg:flex space-x-2">
                    @if($product->cover_image)
                        <div>
                            <img src="{{ asset('storage/' . $product->cover_image) }}"
                                 alt="Cover Thumb"
                                 class="h-24 w-full object-cover border border-gray-300 rounded cursor-pointer"/>
                        </div>
                    @endif
                    @foreach($product->images as $image)
                        <div>
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Thumb Image"
                                 class="h-24 w-full object-cover border border-gray-300 rounded cursor-pointer"/>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Details Section -->
            <div class="lg:w-3/5 w-full p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->title }}</h2>
                    <p class="text-gray-700 mb-4">{{ $product->description }}</p>

                    <div class="space-y-2 text-lg">
                        <p><span class="font-semibold">Price:</span> ₹{{ number_format($product->price, 2) }}</p>
                        <p><span class="font-semibold">Stock:</span> {{ $product->stock }}</p>
                        <p><span class="font-semibold">Status:</span>
                            <span class="capitalize
                                @if($product->status === 'approved') text-green-600
                                @elseif($product->status === 'rejected') text-red-600
                                @else text-yellow-600
                                @endif
                            ">{{ $product->status }}</span>
                        </p>
                        @if($product->admin_note)
                            <p><span class="font-semibold">Admin Note:</span> {{ $product->admin_note }}</p>
                        @endif
                    </div>

                    <hr class="my-6">

                    <h3 class="text-2xl font-semibold mb-4">Seller Details</h3>
                    <div class="text-gray-800 space-y-1">
                        <p><span class="font-semibold">Name:</span> {{ $product->seller->name }}</p>
                        <p><span class="font-semibold">Email:</span> {{ $product->seller->email }}</p>
                        <p><span class="font-semibold">Phone:</span> {{ $product->seller->phone }}</p>
                        <p><span class="font-semibold">Bio:</span> {{ $product->seller->bio }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    @if($product->status === 'pending')
                        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                            <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                                    Approve
                                </button>
                            </form>
                            <button @click="showReject = true"
                                class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                                Reject
                            </button>
                        </div>
                    @else
                        <div>
                            <p class="text-gray-600 italic">This product has already been <span class="capitalize">{{ $product->status }}</span>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div
        x-show="showReject"
        @keydown.escape.window="showReject = false"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
        >
            <button @click="showReject = false"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
            <h3 class="text-xl font-semibold mb-4">Reject Product</h3>
            <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                @csrf
                <textarea name="admin_note" required rows="4"
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Enter reason for rejection..."></textarea>
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" @click="showReject = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <!-- Slick JS -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- Alpine.js (if not already included in layout) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>

    <script>
        $(document).ready(function () {
            $('.main-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                fade: true,
                asNavFor: '.thumb-slider',
                autoplay: true,
                autoplaySpeed: 3000,
            });

            $('.thumb-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                asNavFor: '.main-slider',
                dots: false,
                centerMode: false,
                focusOnSelect: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });
        });
    </script>

@endsection
