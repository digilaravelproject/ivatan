<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeepLinkWebFallbackController extends Controller
{
    public function serveWellKnownFile(string $filename)
    {
        $allowedFiles = [
            'assetlinks.json' => 'application/json',
            'apple-app-site-association' => 'application/json',
        ];

        if (!array_key_exists($filename, $allowedFiles)) {
            abort(404);
        }

        $filePath = public_path('.well-known/' . $filename);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => $allowedFiles[$filename],
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function showPost($id)
    {
        $post = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name as author_name', 'users.username')
            ->where('posts.id', $id)
            ->first();

        $title = $post ? "Post by " . ($post->author_name ?? 'User') . " on iVatan" : "iVatan - Share & Connect";
        $description = $post ? (substr(strip_tags($post->content ?? $post->caption ?? ''), 0, 160) ?: "Check out this post on iVatan app!") : "Explore trending posts, jobs, products and services on iVatan.";
        
        $image = asset('media/logo.jpg');
        if ($post && !empty($post->media_url)) {
            $image = filter_var($post->media_url, FILTER_VALIDATE_URL) ? $post->media_url : asset('storage/' . $post->media_url);
        }

        $deepLink = "ivatan://post/" . $id;
        $url = url('/post/' . $id);

        return view('web.deeplink_fallback', compact('title', 'description', 'image', 'deepLink', 'url'));
    }

    public function showProfile($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        $title = $user ? ($user->name . " (@" . ($user->username ?? 'user') . ") on iVatan") : "User Profile on iVatan";
        $description = $user ? ($user->bio ?? "View " . $user->name . "'s profile and posts on iVatan.") : "Connect with people on iVatan.";
        
        $image = asset('media/logo.jpg');
        if ($user && !empty($user->profile_picture)) {
            $image = filter_var($user->profile_picture, FILTER_VALIDATE_URL) ? $user->profile_picture : asset('storage/' . $user->profile_picture);
        }

        $deepLink = "ivatan://profile/" . $id;
        $url = url('/profile/' . $id);

        return view('web.deeplink_fallback', compact('title', 'description', 'image', 'deepLink', 'url'));
    }

    public function showProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        $title = $product ? ($product->name ?? $product->title ?? "Product on iVatan") : "Buy & Sell on iVatan Marketplace";
        $description = $product ? (substr(strip_tags($product->description ?? ''), 0, 160) ?: "Check out this item on iVatan Marketplace!") : "Explore products and services on iVatan Marketplace.";

        $image = asset('media/logo.jpg');
        if ($product && !empty($product->image_url ?? $product->image ?? null)) {
            $img = $product->image_url ?? $product->image;
            $image = filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/' . $img);
        }

        $deepLink = "ivatan://product/" . $id;
        $url = url('/product/' . $id);

        return view('web.deeplink_fallback', compact('title', 'description', 'image', 'deepLink', 'url'));
    }

    public function showJob($id)
    {
        $job = DB::table('jobs')->where('id', $id)->first();

        $title = $job ? ($job->title ?? "Job Opportunity on iVatan") : "Careers & Jobs on iVatan";
        $description = $job ? (substr(strip_tags($job->description ?? ''), 0, 160) ?: "Apply for this job on iVatan!") : "Find exciting career opportunities on iVatan QuickHire.";

        $image = asset('media/logo.jpg');

        $deepLink = "ivatan://job/" . $id;
        $url = url('/job/' . $id);

        return view('web.deeplink_fallback', compact('title', 'description', 'image', 'deepLink', 'url'));
    }
}
