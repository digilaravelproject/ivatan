@extends('admin.layouts.app')
@section('title', 'Product Details')

@section('head')
    <!-- Slick CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
@endsection

@section('content')
    <div class="container px-6 py-8 mx-auto" x-data="{ showReject: false }">
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="lg:flex">
                <!-- Slider Section -->
                <div class="w-full lg:w-2/5">
                    <!-- Main Product Slider -->
                    <div class="main-slider">
                        @if ($product->cover_image)
                            <div>
                                <img src="{{ asset('storage/' . $product->cover_image) }}" alt="Cover Image"
                                    class="object-cover w-full rounded-t-lg h-96 lg:rounded-none" />
                            </div>
                        @endif
                        @foreach ($product->images as $image)
                            <div>
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image"
                                    class="object-cover w-full h-96" />
                            </div>
                        @endforeach
                    </div>

                    <!-- Thumbnail Slider -->
                    <div class="hidden mt-4 space-x-2 thumb-slider lg:flex">
                        @if ($product->cover_image)
                            <div>
                                <img src="{{ asset('storage/' . $product->cover_image) }}" alt="Cover Thumb"
                                    class="object-cover w-full h-24 border border-gray-300 rounded cursor-pointer" />
                            </div>
                        @endif
                        @foreach ($product->images as $image)
                            <div>
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumb Image"
                                    class="object-cover w-full h-24 border border-gray-300 rounded cursor-pointer" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Details Section -->
                <div class="flex flex-col justify-between w-full p-6 lg:w-3/5">
                    <div>
                        <h2 class="mb-4 text-3xl font-bold text-gray-900">{{ $product->title }}</h2>
                        <p class="mb-4 text-gray-700">{{ $product->description }}</p>

                        <div class="space-y-2 text-lg">
                            <p><span class="font-semibold">Price:</span> ₹{{ number_format($product->price, 2) }}</p>
                            <p><span class="font-semibold">Stock:</span> {{ $product->stock }}</p>
                            <p><span class="font-semibold">Status:</span>
                                <span
                                    class="capitalize
                                @if ($product->status === 'approved') text-green-600
                                @elseif($product->status === 'rejected') text-red-600
                                @else text-yellow-600 @endif
                            ">{{ $product->status }}</span>
                            </p>
                            @if ($product->admin_note)
                                <p><span class="font-semibold">Admin Note:</span> {{ $product->admin_note }}</p>
                            @endif
                        </div>

                        <hr class="my-6">

                        <h3 class="mb-4 text-2xl font-semibold">Seller Details</h3>
                        <div class="space-y-1 text-gray-800">
                            <p><span class="font-semibold">Name:</span> {{ $product->seller->name }}</p>
                            <p><span class="font-semibold">Email:</span> {{ $product->seller->email }}</p>
                            <p><span class="font-semibold">Phone:</span> {{ $product->seller->phone }}</p>
                            <p><span class="font-semibold">Bio:</span> {{ $product->seller->bio }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        @if ($product->status === 'pending')
                            <div class="flex flex-col space-y-3 sm:flex-row sm:space-x-4 sm:space-y-0">
                                <form action="{{ route('admin.products.approve', $product) }}" method="POST"
                                    class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-3 font-semibold text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                                        Approve
                                    </button>
                                </form>
                                <button @click="showReject = true"
                                    class="w-full px-4 py-3 font-semibold text-white transition bg-red-600 rounded-lg sm:w-auto hover:bg-red-700">
                                    Reject
                                </button>
                            </div>
                        @else
                            <div>
                                <p class="italic text-gray-600">This product has already been <span
                                        class="capitalize">{{ $product->status }}</span>.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showReject" @keydown.escape.window="showReject = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <button @click="showReject = false"
                    class="absolute text-xl text-gray-500 top-3 right-3 hover:text-gray-700">&times;</button>
                <h3 class="mb-4 text-xl font-semibold">Reject Product</h3>
                <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                    @csrf
                    <textarea name="admin_note" required rows="4"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                        placeholder="Enter reason for rejection..."></textarea>
                    <div class="flex justify-end mt-4 space-x-2">
                        <button type="button" @click="showReject = false"
                            class="px-4 py-2 transition bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700">Reject</button>
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
        $(document).ready(function() {
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
                responsive: [{
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
