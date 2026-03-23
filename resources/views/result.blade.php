<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result - Blasp Filter</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-lg text-center">

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            ✅ Filter Result
        </h1>

        <!-- Original -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 text-left">
            <p class="text-sm text-gray-500">Original Message</p>
            <p class="text-gray-800 font-medium mt-1">
                {{ $original }}
            </p>
        </div>

        <!-- Filtered -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-left">
            <p class="text-sm text-green-600">Filtered Message</p>
            <p class="text-green-800 font-semibold mt-1">
                {{ $filtered }}
            </p>
        </div>

        <!-- Back Button -->
        <a href="/"
           class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
            ⬅ Back to Home
        </a>

    </div>

</body>
</html>