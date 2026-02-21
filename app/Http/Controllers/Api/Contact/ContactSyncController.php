<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactSyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        // 1. Validation (Structure changed to accept objects)
        $request->validate([
            'contacts' => 'required|array',
            'contacts.*.phone' => 'required',
            'contacts.*.name' => 'nullable', // Phonebook wala name
            'country_code' => 'nullable|string', 
        ]);

        $loggedInUser = Auth::user();
        $inputContacts = $request->input('contacts');
        
        // --- STEP 1: DETECT COUNTRY CODE ---
        $defaultCode = $request->input('country_code');

        if (empty($defaultCode) && $loggedInUser->country_code) {
             $defaultCode = $loggedInUser->country_code;
        }
        
        if (empty($defaultCode)) {
            $defaultCode = '+91'; 
        }

        // --- STEP 2: PREPARE DATA MAPS ---
        
        // Hamein 3 cheezein track karni hain:
        // 1. Searchable Number (DB query ke liye)
        // 2. Formatted Number (Display ke liye)
        // 3. Payload Name (Unregistered users ke liye)
        
        $searchableToPayloadMap = []; // Key: CleanNumber => Value: {name, formatted_phone}
        $numbersToSearch = [];

        foreach ($inputContacts as $contact) {
            $rawPhone = $contact['phone'];
            $payloadName = $contact['name'] ?? 'Unknown';

            // Cleaning logic
            $clean = preg_replace('/[^0-9+]/', '', $rawPhone);
            
            if (empty($clean)) continue;

            $searchable = $clean;
            $formatted = $clean;

            // CASE A: Number starts with '+'
            if (str_starts_with($clean, '+')) {
                $formatted = $clean; 
                // Agar DB me number bina code ke store hai to code hata do
                if (str_starts_with($clean, $defaultCode)) {
                    $searchable = substr($clean, strlen($defaultCode));
                }
            } 
            // CASE B: Local Format
            else {
                $searchable = $clean;
                $formatted = $defaultCode . $clean;
            }

            // Map me store karo
            $numbersToSearch[] = $searchable;
            $searchableToPayloadMap[$searchable] = [
                'payload_name' => $payloadName,
                'formatted_phone' => $formatted
            ];
        }

        $numbersToSearch = array_unique($numbersToSearch);

        // --- STEP 3: DATABASE QUERY ---
        
        // Batch query for performance
        $matchedUsers = User::whereIn('phone', $numbersToSearch)
            ->select(['id', 'name', 'username', 'email', 'phone', 'profile_photo_path', 'country_code']) // columns check karlena
            ->withCount([
                'followers as is_following' => function ($q) use ($loggedInUser) {
                    $q->where('follower_id', $loggedInUser->id);
                },
                'following as is_follower' => function ($q) use ($loggedInUser) {
                    $q->where('following_id', $loggedInUser->id);
                }
            ])
            ->get()
            ->keyBy('phone'); // Fast lookup ke liye keyBy use kiya

        // --- STEP 4: SEPARATE & FORMAT ---

        $registeredList = [];
        $unregisteredList = [];
        
        // Processed numbers ko track karne ke liye
        $processedNumbers = [];

        // 1. Registered Users Process karo
        foreach ($matchedUsers as $phone => $user) {
            $processedNumbers[] = $phone; // Mark as processed
            $isMine = ($user->id === $loggedInUser->id);
            
            // Full Phone reconstruction based on User DB
            $userFullPhone = ($user->country_code ?? $defaultCode) . $user->phone;

            $registeredList[] = [
                'type' => 'registered',
                'id' => $user->id,
                'name' => $isMine ? 'You' : $user->name, // Server Name
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $userFullPhone,
                'avatar' => $user->profile_photo_url, // Laravel Accessor
                'is_mine' => $isMine,
                'is_invite' => false,
                'is_following' => (bool) $user->is_following,
                'is_follower' => (bool) $user->is_follower,
            ];
        }

        // 2. Unregistered Users (Invite) Process karo
        foreach ($numbersToSearch as $searchable) {
            // Agar ye number abhi tak process nahi hua (matlab DB me nahi mila)
            if (!$matchedUsers->has($searchable)) {
                
                $metaData = $searchableToPayloadMap[$searchable] ?? null;
                if (!$metaData) continue;

                $unregisteredList[] = [
                    'type' => 'unregistered', // or 'invite'
                    'id' => null,
                    'name' => $metaData['payload_name'], // Payload Name
                    'username' => null,
                    'email' => null,
                    'phone' => $metaData['formatted_phone'],
                    'avatar' => null,
                    'is_mine' => false,
                    'is_invite' => true,
                    'is_following' => false,
                    'is_follower' => false,
                ];
            }
        }

        // --- STEP 5: SORTING (A-Z) ---
        
        $sortFunction = function ($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        };

        usort($registeredList, $sortFunction);
        usort($unregisteredList, $sortFunction);

        // --- STEP 6: MERGE & RETURN ---
        
        $fullList = collect(array_merge($registeredList, $unregisteredList));

        return response()->json([
            'status' => true,
            'message' => 'Contacts synced successfully',
            'count_registered' => count($registeredList),
            'count_invite' => count($unregisteredList),
            'total_count' => $fullList->count(),
            'data' => $fullList->values(),
        ]);
    }
}