<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderation Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-200">
    <div class="mx-auto max-w-6xl px-6 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">🛡️ Moderation Dashboard</h1>
                <p class="mt-2 text-sm text-slate-400">Manage custom bad words, watch reputation changes, and monitor mute risk.</p>
            </div>
            <a href="/" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold hover:bg-slate-800">Back to Home</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-400">✅ {{ session('success') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
                <h2 class="mb-4 text-xl font-semibold">➕ Add Custom Bad Word</h2>
                <form method="POST" action="{{ route('moderation.words.store') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="word" placeholder="e.g. hell" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3 text-white focus:border-cyan-500 focus:outline-none">
                    <button class="w-full rounded-lg bg-cyan-600 px-4 py-3 font-semibold hover:bg-cyan-700">Save Word</button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
                <h2 class="mb-4 text-xl font-semibold">📊 Reputation Analytics</h2>
                <ul class="space-y-3">
                    @foreach($users as $user)
                        <li class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950 p-3">
                            <div>
                                <p class="font-semibold">{{ $user->name }}</p>
                                <p class="text-sm text-slate-400">Profanity hits: {{ $user->profanity_hits }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold {{ $user->reputation_score <= 50 ? 'text-red-400' : 'text-emerald-400' }}">Score: {{ $user->reputation_score }}</p>
                                <p class="text-xs text-slate-400">Muted: {{ $user->isMuted() ? 'Yes' : 'No' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <h2 class="mb-4 text-xl font-semibold">🧾 Current Bad Word List</h2>
            <div class="flex flex-wrap gap-2">
                @forelse($badWords as $word)
                    <span class="rounded-full border border-slate-700 bg-slate-950 px-3 py-1 text-sm">{{ $word->word }}</span>
                @empty
                    <p class="text-slate-400">No custom bad words yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
