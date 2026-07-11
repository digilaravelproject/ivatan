<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class UserPostService
{
    public function __construct(
        private PostMediaService $mediaService
    ) {}

    /**
     * Create a core post (handles DB transaction, base fields, and media upload).
     *
     * @param User $user The creator of the post.
     * @param array $data Validated data (type, caption, visibility).
     * @param array|UploadedFile|null $mediaFiles Files to upload.
     * @param array $additionalFields Any additional fields (e.g. is_exclusive, price).
     * @return UserPost
     * @throws Exception
     */
    public function createPost(User $user, array $data, mixed $mediaFiles = null, array $additionalFields = []): UserPost
    {
        return DB::transaction(function () use ($user, $data, $mediaFiles, $additionalFields) {
            $postAttributes = array_merge([
                'user_id' => $user->id,
                'uuid' => Str::uuid(),
                'type' => $data['type'],
                'caption' => $data['caption'] ?? null,
                'visibility' => $data['visibility'] ?? 'public',
                'status' => 'active',
                'view_count' => 0,
                'like_count' => 0,
                'comment_count' => 0,
            ], $additionalFields);

            $post = UserPost::create($postAttributes);

            if ($mediaFiles) {
                if (!is_array($mediaFiles)) {
                    $mediaFiles = [$mediaFiles];
                }

                $uploadInfo = $this->mediaService->uploadMedia($post, $mediaFiles);

                if ($uploadInfo->hasAny()) {
                    $detectedType = $this->mediaService->detectPostType($uploadInfo, $data['type']);

                    if ($detectedType) {
                        $post->update(['type' => $detectedType]);
                    }
                }
            }

            return $post;
        });
    }
}
