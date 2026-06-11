<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserEnquiry;
use App\Services\NotificationService;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class EnquiryService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Submit a new enquiry
     */
    public function storeEnquiry(array $data, ?int $userId)
    {
        if ($userId) {
            $data['user_id'] = $userId;
            
            // Check self-enquiry
            if (isset($data['seller_id']) && (int)$data['seller_id'] === $userId) {
                throw ValidationException::withMessages([
                    'seller_id' => ['You cannot submit an enquiry for your own product or service.']
                ]);
            }
        }

        $enquiry = UserEnquiry::create($data);
        return $enquiry->load(['service', 'product', 'seller']);
    }

    /**
     * List enquiries made by the buyer
     */
    public function listMyEnquiries($user)
    {
        return UserEnquiry::with(['service', 'product', 'seller'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);
    }

    /**
     * List enquiries for the seller
     */
    public function listSellerEnquiries($user)
    {
        if (!$user->is_seller) {
            throw new AuthorizationException('Only sellers can view enquiries');
        }

        return UserEnquiry::with(['service', 'product', 'seller', 'user'])
            ->where('seller_id', $user->id)
            ->latest()
            ->paginate(15);
    }

    /**
     * Get enquiry dashboard stats for seller
     */
    public function getStats($user)
    {
        if (!$user->is_seller) {
            throw new AuthorizationException('Unauthorized');
        }

        return [
            'total' => UserEnquiry::where('seller_id', $user->id)->count(),
            'pending' => UserEnquiry::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'replied' => UserEnquiry::where('seller_id', $user->id)->where('status', 'replied')->count(),
            'closed' => UserEnquiry::where('seller_id', $user->id)->where('status', 'closed')->count(),
        ];
    }

    /**
     * Update enquiry status
     */
    public function updateStatus(string $identifier, array $data, $user)
    {
        $enquiry = UserEnquiry::with('user')
            ->where(function($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('uuid', $identifier);
            })->firstOrFail();

        if ($enquiry->seller_id !== $user->id) {
            throw new AuthorizationException('Unauthorized');
        }

        $enquiry->update([
            'status' => $data['status'],
            'reply_message' => $data['reply_message'] ?? $enquiry->reply_message,
        ]);

        // Notify the user who made the enquiry
        if ($enquiry->user) {
            $this->notificationService->sendToUser(
                $enquiry->user,
                'enquiry_update',
                [
                    'enquiry_id' => $enquiry->id,
                    'status' => $data['status'],
                    'message' => "Your enquiry status has been updated to {$data['status']}.",
                    'reply' => $data['reply_message'] ?? null,
                ]
            );
        }

        return $enquiry;
    }

    /**
     * Delete/Archive enquiry
     */
    public function deleteEnquiry(string $identifier, $user)
    {
        $enquiry = UserEnquiry::where(function($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('uuid', $identifier);
            })->firstOrFail();

        if ($enquiry->seller_id !== $user->id) {
            throw new AuthorizationException('Unauthorized');
        }

        $enquiry->delete();
    }
}
