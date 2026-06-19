<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhonePe Payment Gateway Test Panel</title>
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-slate-100">

    <div class="w-full max-w-md bg-slate-900/80 backdrop-blur-md border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8 relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>

        <!-- Header -->
        <div class="text-center mb-8 relative z-10">
            <span class="px-3 py-1 bg-purple-500/10 border border-purple-500/30 text-purple-400 rounded-full text-xs font-semibold tracking-wider uppercase">Temporary Test Panel</span>
            <h1 class="text-2xl sm:text-3xl font-bold mt-3 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent">PhonePe Gateway Test</h1>
            <p class="text-slate-400 text-xs mt-2">Test your integration seamlessly without authentication</p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-xs flex items-center">
                <span class="mr-2">❌</span> {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('temp-payment.pay') }}" method="POST" class="space-y-6 relative z-10">
            @csrf
            
            <!-- Payment Mode Selector -->
            <div>
                <label class="block text-slate-300 text-xs font-semibold uppercase tracking-wider mb-2">Select Payment Mode</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="purchase" class="sr-only peer" checked onclick="toggleMode('purchase')">
                        <div class="p-3 text-center rounded-2xl border border-slate-800 bg-slate-950/40 text-slate-400 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 peer-checked:text-purple-400 hover:border-slate-700 transition duration-200">
                            <div class="text-base font-semibold">One-Time</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Simple Purchase</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="subscription" class="sr-only peer" onclick="toggleMode('subscription')">
                        <div class="p-3 text-center rounded-2xl border border-slate-800 bg-slate-950/40 text-slate-400 peer-checked:border-pink-500 peer-checked:bg-pink-500/10 peer-checked:text-pink-400 hover:border-slate-700 transition duration-200">
                            <div class="text-base font-semibold">Subscription</div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Recurring Mandate</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Customer Details Group -->
            <div class="space-y-4 bg-slate-950/30 border border-slate-800/60 p-4 rounded-2xl">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-2">Customer Details</div>
                
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="name" value="Test User" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" value="test@example.com" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Phone Number (UPI linked)</label>
                    <input type="text" name="phone" value="9999999999" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>
            </div>

            <!-- Amount Section (Conditional) -->
            <div id="amount-section">
                <label class="block text-slate-300 text-xs font-semibold uppercase tracking-wider mb-2">Enter Amount (INR)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-500 text-sm font-semibold">₹</span>
                    <input type="number" name="amount" id="amount" value="10.00" min="1" step="0.01" class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-8 pr-3 py-2 text-sm text-slate-200 font-semibold focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>
            </div>

            <!-- Subscription Plan Section (Conditional, Hidden initially) -->
            <div id="plan-section" class="hidden">
                <label class="block text-slate-300 text-xs font-semibold uppercase tracking-wider mb-2">Select Subscription Plan</label>
                <select name="plan_id" id="plan_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 font-medium">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">
                            {{ $plan->name }} - ₹{{ number_format($plan->price, 2) }} / {{ $plan->duration_days }} Days
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pay Button -->
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500 text-white rounded-2xl font-bold text-xs uppercase tracking-wider hover:opacity-90 active:scale-[0.98] transition duration-150 shadow-lg shadow-purple-500/20">
                Proceed to Pay via PhonePe
            </button>
        </form>
    </div>

    <script>
        function toggleMode(mode) {
            const amountSection = document.getElementById('amount-section');
            const planSection = document.getElementById('plan-section');
            const amountInput = document.getElementById('amount');
            const planSelect = document.getElementById('plan_id');

            if (mode === 'purchase') {
                amountSection.classList.remove('hidden');
                planSection.classList.add('hidden');
                amountInput.required = true;
                planSelect.required = false;
            } else {
                amountSection.classList.add('hidden');
                planSection.classList.remove('hidden');
                amountInput.required = false;
                planSelect.required = true;
            }
        }
    </script>
</body>
</html>
