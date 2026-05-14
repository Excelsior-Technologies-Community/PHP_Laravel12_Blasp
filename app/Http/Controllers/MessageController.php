<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Blaspsoft\Blasp\Facades\Blasp;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::query();

        // SEARCH FUNCTIONALITY
        if ($request->search) {
            $query->where('original', 'like', '%' . $request->search . '%')
                ->orWhere('filtered', 'like', '%' . $request->search . '%');
        }

        // PAGINATION
        $messages = $query->oldest()->paginate(2);

        return view('form', compact('messages'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'message' => 'required'
        ]);

        $original = $request->message;
        $filtered = Blasp::check($original)->getCleanString();

        Message::create([
            'original' => $original,
            'filtered' => $filtered
        ]);

        return redirect('/')
            ->with('success', 'Message filtered successfully!');
    }

    // DELETE FUNCTION
    public function delete($id)
    {
        Message::findOrFail($id)->delete();

        return redirect('/')
            ->with('success', 'Message deleted successfully!');
    }
}