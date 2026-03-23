<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blasp Filter</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center py-10">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">🧩 Blasp Filter</h1>
        <p class="text-gray-500 mt-2">Clean your messages from profanity</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-lg rounded-2xl p-6 w-full max-w-md">
        <form method="POST" action="{{ route('check') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enter Message
                </label>
                <input
                    type="text"
                    name="message"
                    placeholder="Type your message..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200"
            >
                Check Message
            </button>
        </form>
    </div>

    <!-- Messages Section -->
    <div class="w-full max-w-2xl mt-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            Saved Messages
        </h2>

        @forelse($messages as $msg)
            <div class="bg-white shadow-md rounded-xl p-4 mb-4 border border-gray-200">
                <p class="text-gray-700">
                    <span class="font-semibold text-gray-900">Original:</span>
                    {{ $msg->original }}
                </p>

                <p class="text-gray-700 mt-1">
                    <span class="font-semibold text-green-600">Filtered:</span>
                    {{ $msg->filtered }}
                </p>
            </div>
        @empty
            <p class="text-gray-500">No messages found.</p>
        @endforelse
    </div>

</body>
</html>