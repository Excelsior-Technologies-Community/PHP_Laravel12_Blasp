# PHP_Laravel12_Blasp

## Introduction

PHP_Laravel12_Blasp is a Laravel 12 application that demonstrates how to integrate the Blasp package for profanity filtering.

This project allows users to input messages, automatically filters offensive words using Blasp, and stores both original and filtered messages in the database. It also displays all saved messages in a clean and modern UI.

The project follows Laravel best practices including MVC architecture, database migrations, and package integration.

---

## Project Overview

This project is designed to:

- Integrate the Blasp profanity filtering package in Laravel 12
- Validate and process user input
- Filter offensive words from messages
- Store both original and filtered messages in a MySQL database
- Display saved messages with a modern UI using Tailwind CSS

The system works as follows:

1. User enters a message
2. The message is processed using Blasp
3. Offensive words are replaced with mask characters (e.g. ****)
4. Both original and filtered messages are saved in the database
5. All messages are displayed on the UI

---

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Blasp "12.*"
cd PHP_Laravel12_Blasp
```
---

## Step 2: Database Setup

Update .env:

```.env
DB_DATABASE=blasp_db
DB_USERNAME=root
DB_PASSWORD=
```
Run Migration Command:

```bash
php artisan migrate
```

---

## Step 3: Install Blasp Package

```bash
composer require blaspsoft/blasp
```

---

## Step 4: Publish Configuration

```bash
php artisan vendor:publish --tag="blasp"
php artisan vendor:publish --tag="blasp-config"
php artisan vendor:publish --tag="blasp-languages"
```
---

## Step 5: Create Model & Migration

```bash
php artisan make:model Message -m
```

This Creates:

- app/Models/Message.php	
- database/migrations/xxxx_xx_xx_create_messages_table.php

---

## Step 6: Migration Table

File: database/migrations/xxxx_xx_xx_create_messages_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('original');
            $table->text('filtered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
```

---

## Step 7: Model

File: app/Models/Message.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Allow mass assignment
    protected $fillable = [
        'original',
        'filtered'
    ];
}
```
---

## Step 8: Create Controller

```bash
php artisan make:controller MessageController
```

File: app/Http/Controllers/MessageController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Blaspsoft\Blasp\Facades\Blasp;

class MessageController extends Controller
{
    /**
     * Show form + all saved messages
     */
    public function index()
    {
        $messages = Message::latest()->get();
        return view('form', compact('messages'));
    }

    /**
     * Handle form submission
     */
    public function check(Request $request)
    {
        // Validation
        $request->validate([
            'message' => ['required']
        ]);

        // Original input
        $original = $request->message;

        // Apply Blasp filter
        $filtered = Blasp::check($original)->getCleanString();

        // Save into database
        Message::create([
            'original' => $original,
            'filtered' => $filtered
        ]);

        // Return result page
        return view('result', compact('original', 'filtered'));
    }
}
```

---

## Step 9: Routes

File: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MessageController;

Route::get('/', [MessageController::class, 'index']);
Route::post('/check', [MessageController::class, 'check'])->name('check');
```

---

## Step 10: Blade Files

### form.blade.php

File: resources/views/form.blade.php

```blade
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
```

### result.blade.php

File: resources/views/result.blade.php

```blade
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
```
---

## Step 11: Run Project

Run Laravel server:

```bash
php artisan serve
```
Open in browser:

```bash
http://127.0.0.1:8000
```
---
## Output

<img src="screenshots/Screenshot 2026-03-23 110339.png" width="1000">

## Project Structure

```
PHP_Laravel12_Blasp/
│
├── app/
│   ├── Models/
│   │   └── Message.php
│   └── Http/
│       └── Controllers/
│           └── MessageController.php
│
├── config/
│   ├── blasp.php
│   └── languages/
│
├── database/
│   └── migrations/
│       └── create_messages_table.php
│
├── resources/
│   └── views/
│       ├── form.blade.php
│       └── result.blade.php
│
├── routes/
│   └── web.php
│
│
└── .env
```
---

Your PHP_Laravel12_Blasp Project is now ready!



