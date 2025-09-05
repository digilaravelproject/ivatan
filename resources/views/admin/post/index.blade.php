@extends('admin.layouts.app')
@section('title', 'All Posts')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold mb-4">All Posts</h2>
        <div class="bg-white rounded-xl shadow p-6">
            <div id="posts-container">
                @forelse($posts as $post)
                    <div class="border rounded-lg overflow-hidden shadow-sm mb-4">
                        <!-- Post Header: Caption and Post Time -->
                        <div class="p-4">
                            <p class="font-semibold text-gray-800">{{ $post->caption ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</p>
                        </div>

                        <!-- Media Section: Handle Carousel for Images -->
                        <div class="w-full h-48 bg-gray-200">
                            @if ($post->media && isset($post->media['images']) && count($post->media['images']) > 0)
                                <img src="{{ asset('storage/' . $post->media['images'][0]) }}"
                                     alt="post image" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" alt="default image"
                                     class="w-full h-full object-cover" loading="lazy">
                            @endif
                        </div>

                        <!-- Post Footer: Likes and Comments -->
                        <div class="p-4 flex justify-between items-center text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-thumbs-up mr-2"></i> <!-- Like icon -->
                                <span>{{ $post->like_count }} Likes</span>
                                <button class="text-xs text-blue-500 ml-2" onclick="showLikesModal({{ $post->id }})">Show Likes</button>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-comment-dots mr-2"></i> <!-- Comment icon -->
                                <span>{{ $post->comment_count }} Comments</span>
                                <button class="text-xs text-blue-500 ml-2" onclick="showCommentsModal({{ $post->id }})">Show Comments</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 col-span-4">No posts available</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Likes Modal -->
    <div id="likes-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">Users Who Liked This Post</h3>
            <ul id="liked-users-list"></ul>
            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded" onclick="closeLikesModal()">Close</button>
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="comments-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">Post Comments</h3>
            <ul id="comments-list"></ul>
            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded" onclick="closeCommentsModal()">Close</button>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Show Likes Modal
    function showLikesModal(postId) {
        const likedUsers = @json($posts->mapWithKeys(function ($post) {
            return [$post->id => $post->liked_by];
        }));

        const users = likedUsers[postId] || [];
        let html = '';
        users.forEach(user => {
            html += `<li>${user}</li>`;
        });

        document.getElementById('liked-users-list').innerHTML = html;
        document.getElementById('likes-modal').classList.remove('hidden');
    }

    // Close Likes Modal
    function closeLikesModal() {
        document.getElementById('likes-modal').classList.add('hidden');
    }

    // Show Comments Modal
    function showCommentsModal(postId) {
        const comments = @json($posts->mapWithKeys(function ($post) {
            return [$post->id => $post->commented_by];
        }));

        const commentData = comments[postId] || [];
        let html = '';
        commentData.forEach(comment => {
            html += `<li><strong>${comment.user}:</strong> ${comment.comment} <span>(${comment.like_count} Likes)</span></li>`;
        });

        document.getElementById('comments-list').innerHTML = html;
        document.getElementById('comments-modal').classList.remove('hidden');
    }

    // Close Comments Modal
    function closeCommentsModal() {
        document.getElementById('comments-modal').classList.add('hidden');
    }

    // Delete comment
    function deleteComment(commentId) {
        if (confirm('Are you sure you want to delete this comment?')) {
            $.ajax({
                url: `/admin/comments/${commentId}`, // Your route for deleting comments
                method: 'DELETE',
                success: function(response) {
                    alert(response.message);
                    // Refresh the page or update UI as necessary
                    window.location.reload();
                },
                error: function(error) {
                    alert('Error deleting comment!');
                }
            });
        }
    }
</script>
@endsection
