<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileBadgesController extends Controller
{
    /**
     * Get the max badges allowed for a user.
     */
    protected function getMaxBadges($user)
    {
        $isDemo = $user->demo ?? false;
        $userType = $user->type ?? "basic";
        if ($isDemo) {
            return config('appoptions.badges_per_demo_user');
        } elseif ($userType === 'premium') {
            return config('appoptions.badges_per_premium_user');
        } else {
            return config('appoptions.badges_per_basic_user');
        }
    }
    /**
     * Display the user's badges page.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = $request->user();
        $badges = $user->badges()->orderBy('id')->get();
        $maxBadges = $this->getMaxBadges($user);
        return view('profile-badges', [
            'badges' => $badges,
            'maxBadges' => $maxBadges,
        ]);
    }

    public function add(Request $request)
    {
        $user = $request->user();
        $maxBadges = $this->getMaxBadges($user);
        $count = $user->badges()->count();
        if ($count >= $maxBadges) {
            return redirect()->route('profile.badges')->withErrors('Badge limit reached.');
        }
        $request->validate([
            'name' => 'required|string|max:50',
        ]);
        // Find the lowest unused badge_id for this user
        $usedIds = $user->badges()->pluck('badge_id')->toArray();
        $badgeId = 1;
        while (in_array($badgeId, $usedIds)) {
            $badgeId++;
        }
        $user->badges()->create([
            'name' => $request->input('name'),
            'badge_id' => $badgeId,
        ]);
        return redirect()->route('profile.badges');
    }

    public function rename(Request $request, $badgeId)
    {
        $user = $request->user();
        $badge = $user->badges()->findOrFail($badgeId);
        $request->validate([
            'name' => 'required|string|max:50',
        ]);
        $badge->name = $request->input('name');
        $badge->save();
        return redirect()->route('profile.badges');
    }
    
    public function delete(Request $request, $badgeId)
    {
        $user = $request->user();
        $badge = $user->badges()->findOrFail($badgeId);
        $badge->delete();

        // Remove badge_id from all items that used this badge (remove all occurrences)
        $items = Item::whereJsonContains('badges', $badge->badge_id)
            ->whereHas('header', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
        foreach ($items as $item) {
            $badges = is_array($item->badges) ? $item->badges : json_decode($item->badges, true);
            if (!is_array($badges)) $badges = [];
            $badges = array_values(array_filter($badges, function($b) use ($badge) {
                return $b != $badge->badge_id;
            }));
            $item->badges = $badges;
            $item->save();
        }

        return redirect()->route('profile.badges');
    }
}
