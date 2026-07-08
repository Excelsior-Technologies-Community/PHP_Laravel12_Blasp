<?php

namespace App\Http\Controllers;

use App\Models\BadWord;
use App\Models\Message;
use App\Models\User;
use Blaspsoft\Blasp\Facades\Blasp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::query();

        if ($request->search) {
            $query->where('original', 'like', '%' . $request->search . '%')
                ->orWhere('filtered', 'like', '%' . $request->search . '%');
        }

        $messages = $query->oldest()->paginate(5);

        return view('form', [
            'messages' => $messages,
            'users' => User::latest()->take(8)->get(),
            'badWords' => BadWord::active()->latest()->take(10)->get(),
            'mutedUsers' => User::whereNotNull('muted_until')->where('muted_until', '>', now())->count(),
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'user_name' => 'nullable|string|max:100',
        ]);

        $userName = trim((string) $request->input('user_name', 'Anonymous'));
        $user = User::firstOrCreate(
            ['name' => $userName],
            [
                'email' => strtolower(str_replace(' ', '.', $userName)) . '@local.test',
                'password' => Hash::make('password'),
                'reputation_score' => User::REPUTATION_START,
            ]
        );

        if ($user->isMuted()) {
            return redirect('/')->with('error', 'This user is currently muted for 24 hours.');
        }

        $analysis = $this->analyzeMessage($request->message);
        $filtered = $analysis['filtered'];

        Message::create([
            'original' => $request->message,
            'filtered' => $filtered,
        ]);

        if ($analysis['has_profanity']) {
            $user->applyProfanityPenalty(count($analysis['matches']));

            return redirect('/')
                ->with('warning', 'Profanity detected. Reputation reduced and the user may be muted at 50 points or below.');
        }

        return redirect('/')
            ->with('success', 'Message filtered successfully!');
    }

    public function delete($id)
    {
        Message::findOrFail($id)->delete();

        return redirect('/')
            ->with('success', 'Message deleted successfully!');
    }

    public function moderationIndex()
    {
        return view('admin.moderation', [
            'badWords' => BadWord::latest()->get(),
            'users' => User::orderByDesc('profanity_hits')->orderByDesc('reputation_score')->take(10)->get(),
        ]);
    }

    public function storeBadWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:30',
        ]);

        BadWord::firstOrCreate([
            'word' => strtolower(trim($request->word)),
        ], [
            'is_active' => true,
        ]);

        return redirect('/admin/moderation')->with('success', 'Bad word added to the moderation list.');
    }

    protected function analyzeMessage(string $message): array
    {
        $blasp = Blasp::check($message);
        $customMatches = [];
        $badWords = BadWord::active()->pluck('word')->filter();

        foreach ($badWords as $word) {
            if (preg_match('/\b' . preg_quote((string) $word, '/') . '\b/i', strtolower($message))) {
                $customMatches[] = strtolower((string) $word);
            }
        }

        $matches = collect(array_merge($customMatches, $blasp->hasProfanity() ? $blasp->getUniqueProfanitiesFound() : []))
            ->map(fn ($word) => trim(strtolower((string) $word)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $filtered = $blasp->hasProfanity() ? $blasp->getCleanString() : $message;

        foreach ($matches as $word) {
            $filtered = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', str_repeat('*', strlen($word)), $filtered);
        }

        return [
            'has_profanity' => count($matches) > 0,
            'matches' => $matches,
            'filtered' => $filtered,
        ];
    }
}