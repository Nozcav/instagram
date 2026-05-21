<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user()->load(['profile', 'posts', 'friends']);

        $postsCount = $user->posts->count();
        $friendsCount = $user->friends->count();

        $profile = $user->profile;
        $avatarPath = $profile->avatar ?? null;
        $avatarUrl = $avatarPath ? asset("storage/{$avatarPath}") : null;

        // Build posts with image URLs
        $posts = $user->posts->map(function ($post) {
            $post->image_url = $post->image ? asset("storage/{$post->image}") : null;
            return $post;
        })->values();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $profile->username ?? $user->username ?? $user->name,
            'bio' => $profile->bio ?? '',
            'avatar' => $avatarPath,
            'avatar_url' => $avatarUrl,
            'posts_count' => $postsCount,
            'followers_count' => $friendsCount,
            'following_count' => $friendsCount,
            'posts' => $posts,
        ]);
    }

    public function avatar(Request $request)
    {
        $data = $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            $profile = $user->profile()->create([
                'username' => $user->username ?? $user->email,
            ]);
        }

        if ($profile->avatar) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $profile->avatar = $path;
        $profile->save();

        return response()->json([
            'avatar' => $path,
            'avatar_url' => asset("storage/{$path}"),
        ]);
    }

    public function createStory(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('stories', 'public');

        $user = $request->user()->load('profile');

        return response()->json([
            'user' => $user,
            'story' => [
                'image' => $path,
                'image_url' => asset("storage/{$path}"),
            ],
        ]);
    }
}
