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