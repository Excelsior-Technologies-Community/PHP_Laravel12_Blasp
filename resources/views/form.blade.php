<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Blasp Filter System</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #0b0f19;
        }

        .card {
            background: #111827;
            border: 1px solid #1f2937;
        }

        .soft-glow {
            box-shadow: 0 0 0 1px #1f2937, 0 10px 30px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>

<body class="text-gray-200 min-h-screen">

    <header class="border-b border-gray-800 bg-[#0b0f19]">
        <div class="max-w-6xl mx-auto px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-wide">🧩 Reputation + Blasp Moderation</h1>
                <p class="text-gray-400 text-sm mt-1">Users lose reputation for profanity and may be muted for 24 hours at 50 points or below.</p>
            </div>
            <a href="{{ route('moderation.index') }}" class="inline-flex items-center rounded-lg border border-cyan-500/30 bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-300 hover:bg-cyan-500/20">
                Open Admin Moderation
            </a>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-6 py-6">

        <div class="grid md:grid-cols-4 gap-5 mb-8">
            <div class="card soft-glow rounded-xl p-5">
                <p class="text-gray-500 text-sm">Total Messages</p>
                <h2 class="text-3xl font-bold mt-2">{{ $messages->total() }}</h2>
            </div>
            <div class="card soft-glow rounded-xl p-5">
                <p class="text-gray-500 text-sm">Tracked Users</p>
                <h2 class="text-3xl font-bold mt-2">{{ $users->count() }}</h2>
            </div>
            <div class="card soft-glow rounded-xl p-5">
                <p class="text-gray-500 text-sm">Muted Users</p>
                <h2 class="text-3xl font-bold mt-2">{{ $mutedUsers }}</h2>
            </div>
            <div class="card soft-glow rounded-xl p-5">
                <p class="text-gray-500 text-sm">Bad Words</p>
                <h2 class="text-3xl font-bold mt-2">{{ $badWords->count() }}</h2>
            </div>
        </div>

        @if(session('success'))
            <div class="card soft-glow rounded-xl p-4 mb-6 text-green-400 border border-green-500/20">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="card soft-glow rounded-xl p-4 mb-6 text-yellow-400 border border-yellow-500/20">
                ⚠️ {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="card soft-glow rounded-xl p-4 mb-6 text-red-400 border border-red-500/20">
                🚫 {{ session('error') }}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="card soft-glow rounded-xl p-6">
                <h2 class="text-lg font-semibold mb-4">➕ Submit a message</h2>

                <form method="POST" action="{{ route('check') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="user_name" placeholder="Enter user name" value="{{ old('user_name') }}"
                        class="w-full p-3 rounded-lg bg-[#0b0f19] border border-gray-700 text-white focus:outline-none focus:border-blue-500">

                    <input type="text" name="message" placeholder="Type your message..."
                        class="w-full p-3 rounded-lg bg-[#0b0f19] border border-gray-700 text-white focus:outline-none focus:border-blue-500">

                    <button class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg font-semibold transition">
                        Submit
                    </button>
                </form>
            </div>

            <div class="card soft-glow rounded-xl p-6">
                <h2 class="text-lg font-semibold mb-4">🔍 Search Messages</h2>

                <form method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages..."
                        class="w-full p-3 rounded-lg bg-[#0b0f19] border border-gray-700 text-white focus:outline-none focus:border-green-500">
                </form>

                <p class="text-xs text-gray-500 mt-2">Search original and filtered text instantly.</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($messages as $msg)
                <div class="card soft-glow rounded-xl p-5 flex justify-between items-start hover:border-gray-600 transition">
                    <div class="space-y-1">
                        <p><span class="text-gray-400">Original:</span> {{ $msg->original }}</p>
                        <p class="text-green-400"><span class="text-gray-400">Filtered:</span> {{ $msg->filtered }}</p>
                    </div>
                    <a href="{{ route('delete', $msg->id) }}" onclick="return confirm('Delete this message?')" class="text-red-400 hover:text-red-300 text-sm font-semibold">
                        Delete
                    </a>
                </div>
            @empty
                <div class="card soft-glow rounded-xl p-6 text-center text-gray-500">No messages found</div>
            @endforelse
        </div>

        <div class="mt-6 card soft-glow rounded-xl p-4">
            {{ $messages->links() }}
        </div>
    </div>
</body>
</html>