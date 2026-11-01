@props(['order', 'readOnly' => false])

{{-- Kotak Live Chat Order --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[460px]" id="orderChatBox">
    {{-- Header Chat --}}
    <div class="px-5 py-3.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <span class="text-xl">💬</span>
            <div>
                <h3 class="font-bold text-sm leading-tight">Live Chat Pesanan #{{ $order->id }}</h3>
                <p class="text-[11px] text-orange-100">
                    @if($readOnly)
                        👁️ Mode Pantau Admin (Hanya Lihat)
                    @else
                        Komunikasi langsung Customer & Driver
                    @endif
                </p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/20 text-white">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Live
        </span>
    </div>

    {{-- Pesan Area (Scrollable) --}}
    <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50/70" id="chatMessagesContainer">
        <div id="chatLoadingNotice" class="text-center py-6 text-gray-400 text-xs">
            Memuat obrolan...
        </div>
    </div>

    {{-- Form Input / Read-Only Notice --}}
    @if(!$readOnly)
    <div class="p-3 bg-white border-t border-gray-100">
        <form id="orderChatForm" class="flex items-center gap-2">
            @csrf
            <input type="text" id="chatMessageInput" placeholder="Ketik pesan untuk driver/customer..." required autocomplete="off"
                class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400">
            <button type="submit" id="chatSendBtn"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition flex items-center gap-1 shadow-sm shrink-0">
                <span>Kirim</span>
                <span>➤</span>
            </button>
        </form>
    </div>
    @else
    <div class="p-3 bg-gray-50 border-t border-gray-200 text-center text-xs text-gray-500 font-medium">
        🔒 Form pesan dinonaktifkan: Anda masuk sebagai Admin dalam mode pantau riwayat.
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderId = {{ $order->id }};
    const messagesUrl = "{{ route('orders.chat.messages', $order) }}";
    const sendUrl = "{{ route('orders.chat.send', $order) }}";
    const csrfToken = "{{ csrf_token() }}";
    const readOnly = {{ $readOnly ? 'true' : 'false' }};

    const container = document.getElementById('chatMessagesContainer');
    const loadingNotice = document.getElementById('chatLoadingNotice');
    const form = document.getElementById('orderChatForm');
    const input = document.getElementById('chatMessageInput');
    const sendBtn = document.getElementById('chatSendBtn');

    let lastMessageId = 0;
    let isFirstLoad = true;

    // Render pesan ke dalam container chat
    function renderMessage(msg) {
        const isMe = msg.is_me;
        const div = document.createElement('div');
        div.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} message-item`;
        div.dataset.id = msg.id;

        // Role badge
        let roleBadge = '';
        if (msg.sender_role === 'driver') {
            roleBadge = '<span class="text-[9px] bg-green-100 text-green-700 px-1.5 py-0.2 rounded font-bold">Driver</span>';
        } else if (msg.sender_role === 'customer') {
            roleBadge = '<span class="text-[9px] bg-blue-100 text-blue-700 px-1.5 py-0.2 rounded font-bold">Customer</span>';
        } else if (msg.sender_role === 'admin') {
            roleBadge = '<span class="text-[9px] bg-purple-100 text-purple-700 px-1.5 py-0.2 rounded font-bold">Admin</span>';
        }

        div.innerHTML = `
            <div class="flex items-center gap-1.5 mb-0.5">
                <span class="text-[10px] font-semibold text-gray-500">${isMe ? 'Anda' : escapeHtml(msg.sender_name)}</span>
                ${roleBadge}
                <span class="text-[9px] text-gray-400">${msg.time}</span>
            </div>
            <div class="max-w-[78%] px-3.5 py-2 rounded-2xl text-xs shadow-sm break-words ${
                isMe
                ? 'bg-orange-500 text-white rounded-br-none'
                : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'
            }">
                ${escapeHtml(msg.message)}
            </div>
        `;

        container.appendChild(div);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    // Scroll otomatis ke bawah
    function scrollToBottom() {
        container.scrollTop = container.scrollHeight;
    }

    // Ambil pesan via Polling AJAX
    function fetchMessages() {
        const url = lastMessageId > 0 ? `${messagesUrl}?after_id=${lastMessageId}` : messagesUrl;

        fetch(url, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (loadingNotice && isFirstLoad) {
                loadingNotice.remove();
            }

            if (data.status === 'success' && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        renderMessage(msg);
                        lastMessageId = msg.id;
                    }
                });
                scrollToBottom();
            } else if (isFirstLoad && (!data.messages || data.messages.length === 0)) {
                container.innerHTML = `
                    <div id="noMsgNotice" class="text-center py-10 text-gray-400 text-xs">
                        <div class="text-3xl mb-1">💬</div>
                        <p>Belum ada obrolan pada pesanan ini.</p>
                        ${!readOnly ? '<p class="text-[11px] text-gray-400 mt-1">Kirim pesan pertama Anda di bawah.</p>' : ''}
                    </div>
                `;
            }

            isFirstLoad = false;
        })
        .catch(err => {
            console.error('Chat polling error:', err);
        });
    }

    // Polling setiap 3.5 detik
    fetchMessages();
    const chatInterval = setInterval(fetchMessages, 3500);

    // Kirim pesan (Jika bukan readOnly)
    if (form && !readOnly) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            const noMsg = document.getElementById('noMsgNotice');
            if (noMsg) noMsg.remove();

            input.disabled = true;
            sendBtn.disabled = true;

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = '';
                    if (data.message.id > lastMessageId) {
                        renderMessage(data.message);
                        lastMessageId = data.message.id;
                        scrollToBottom();
                    }
                } else {
                    alert(data.message || 'Gagal mengirim pesan.');
                }
            })
            .catch(err => {
                console.error('Gagal kirim pesan:', err);
                alert('Terjadi kesalahan jaringan.');
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            });
        });
    }
});
</script>
@endpush

