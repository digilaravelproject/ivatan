@extends('admin.layouts.app')
@section('title', 'Live Chat - ' . $group->name)
@section('content')
<div
    x-data="liveChat()"
    x-init="init()"
    class="flex flex-col bg-white rounded-lg shadow h-[calc(100vh-10rem)]"
>
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b">
        <div>
            <h2 class="text-xl font-bold">{{ $group->name }}</h2>
            <p class="text-sm text-gray-500">
                <span class="inline-block w-2 h-2 rounded-full mr-1"
                      :class="connected ? 'bg-green-500' : 'bg-red-500'"></span>
                <span x-text="connected ? 'Connected' : 'Disconnected'"></span>
                &middot; <span x-text="messages.length + ' messages'"></span>
            </p>
        </div>
        <a href="{{ route('admin.live-chat-groups.show', $group) }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Back to Group
        </a>
    </div>

    {{-- Messages --}}
    <div
        x-ref="messagesContainer"
        class="flex-1 overflow-y-auto px-6 py-4 space-y-3"
        @scroll="handleScroll"
    >
        <template x-if="loading">
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </template>

        <template x-for="(msg, index) in messages" :key="msg.id ? `msg-${msg.id}` : `temp-${index}`">
            <div class="flex" :class="msg.sender_id === userId ? 'justify-end' : 'justify-start'">
                <div class="max-w-[75%]" :class="msg.sender_id === userId ? 'order-1' : ''">
                    {{-- Sender name --}}
                    <p class="text-xs text-gray-400 mb-1 px-1"
                       x-text="msg.sender ? msg.sender.name : 'System'"
                       x-show="msg.sender_id !== userId"></p>

                    {{-- Message bubble --}}
                    <div
                        class="rounded-2xl px-4 py-2 text-sm leading-relaxed break-words"
                        :class="msg.message_type === 'system'
                            ? 'bg-gray-100 text-gray-500 italic text-center text-xs mx-auto'
                            : msg.sender_id === userId
                                ? 'bg-blue-600 text-white rounded-br-md'
                                : 'bg-gray-100 text-gray-800 rounded-bl-md'"
                    >
                        <span x-text="msg.content"></span>
                    </div>

                    {{-- Time --}}
                    <p class="text-[10px] text-gray-400 mt-1 px-1"
                       x-text="msg.sender_id === userId ? formatTime(msg.created_at) : formatTime(msg.created_at)"
                       :class="msg.sender_id === userId ? 'text-right' : ''">
                    </p>
                </div>
            </div>
        </template>

        <div x-ref="scrollAnchor"></div>
    </div>

    {{-- Input --}}
    <div class="border-t px-6 py-4">
        <form @submit.prevent="sendMessage" class="flex gap-3">
            <input
                type="text"
                x-model="newMessage"
                placeholder="Type a message..."
                class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                :disabled="sending"
                @keydown.enter.prevent="sendMessage"
            />
            <button
                type="submit"
                :disabled="!newMessage.trim() || sending"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium
                       hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                <span x-show="!sending">Send</span>
                <span x-show="sending" class="flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Sending
                </span>
            </button>
        </form>
    </div>
</div>

<script>
function liveChat() {
    return {
        messages: [],
        newMessage: '',
        userId: {{ $user->id }},
        chatId: {{ $chat->id }},
        groupId: '{{ $group->id }}',
        loading: true,
        sending: false,
        connected: false,
        echo: null,
        autoScroll: true,

        init() {
            this.fetchMessages();
            this.initEcho();
            this.$watch('messages', () => {
                this.messages = this.messages.filter(
                    (msg, i, arr) => msg.id && i === arr.findIndex(m => m.id === msg.id)
                );
            });
        },

        fetchMessages() {
            this.loading = true;
            axios.get('/admin/live-chat-groups/' + this.groupId + '/chat/messages')
                .then(res => {
                    this.messages = res.data.messages;
                    this.loading = false;
                    this.$nextTick(() => this.scrollToBottom());
                })
                .catch(() => { this.loading = false; });
        },

        initEcho() {
            if (typeof window.Echo === 'undefined') return;

            this.echo = window.Echo.private('chat.' + this.chatId)
                .listen('.message.sent', (e) => {
                    // Don't duplicate messages we just sent
                    if (e.sender && e.sender.id === this.userId) return;
                    if (this.messages.some(m => m.id === e.id)) return;

                    this.messages.push({
                        id: e.id,
                        chat_id: e.chat_id,
                        sender_id: e.sender ? e.sender.id : null,
                        content: e.content,
                        message_type: e.message_type || 'text',
                        created_at: e.created_at,
                        sender: e.sender ? {
                            id: e.sender.id,
                            name: e.sender.name,
                        } : null,
                    });

                    this.$nextTick(() => {
                        if (this.autoScroll) this.scrollToBottom();
                    });
                })
                .subscribed(() => { this.connected = true; })
                .error(() => { this.connected = false; });
        },

        sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const content = this.newMessage;

            axios.post('/admin/live-chat-groups/' + this.groupId + '/chat/messages', { content })
                .then(res => {
                    this.messages.push(res.data.message);
                    this.newMessage = '';
                    this.$nextTick(() => this.scrollToBottom());
                })
                .catch(err => {
                    alert(err.response?.data?.error || 'Failed to send message');
                })
                .finally(() => { this.sending = false; });
        },

        scrollToBottom() {
            const anchor = this.$refs.scrollAnchor;
            if (anchor) anchor.scrollIntoView({ behavior: 'smooth' });
        },

        handleScroll() {
            const el = this.$refs.messagesContainer;
            if (!el) return;
            const threshold = 50;
            this.autoScroll = (el.scrollTop + el.clientHeight >= el.scrollHeight - threshold);
        },

        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endsection
