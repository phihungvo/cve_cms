@php
    $user = auth()->user();
    $unreadCount = $user ? \App\Domains\Notification\Model\UserNotification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count() : 0;
@endphp

<div class="fixed bottom-5 right-5 z-50">
    <!-- Notification Icon with Badge -->
    <button id="notification-toggle"
        class="relative bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9">
            </path>
        </svg>
        @if($unreadCount > 0)
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Popup -->
    <div id="notification-popup"
        class="hidden absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto">
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-2">{{ __('Notifications') }}</h3>
            <div id="notification-list">
                <!-- Notifications will be loaded here via JavaScript -->
                <p class="text-gray-500">{{ __('Loading notifications...') }}</p>
            </div>
            <div class="mt-4">
                <a href="{{ route('notification.index') }}"
                    class="text-blue-500 hover:underline">{{ __('View all notifications') }}</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('notification-toggle');
            const popup = document.getElementById('notification-popup');
            const notificationList = document.getElementById('notification-list');

            // Toggle popup visibility
            toggleButton.addEventListener('click', function () {
                popup.classList.toggle('hidden');
                if (!popup.classList.contains('hidden')) {
                    loadNotifications();
                }
            });

            // Close popup when clicking outside
            document.addEventListener('click', function (event) {
                if (!popup.contains(event.target) && !toggleButton.contains(event.target)) {
                    popup.classList.add('hidden');
                }
            });

            // Load notifications via API
            function loadNotifications() {
                fetch("{{ route('notification.unread') }}", {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            notificationList.innerHTML = '';
                            if (data.data.notifications.length === 0) {
                                notificationList.innerHTML = '<p class="text-gray-500">{{ __("No unread notifications") }}</p>';
                            } else {
                                data.data.notifications.forEach(notification => {
                                    const notificationItem = document.createElement('div');
                                    notificationItem.className = 'p-2 border-b border-gray-200 hover:bg-gray-100';
                                    notificationItem.innerHTML = `
                                            <a href="{{ route('notification.show', '') }}/${notification.id}" class="block">
                                                <h4 class="text-sm font-medium">${notification.title}</h4>
                                                <p class="text-xs text-gray-600">${notification.content.substring(0, 50)}...</p>
                                                <p class="text-xs text-gray-400">${new Date(notification.created_at * 1000).toLocaleString()}</p>
                                            </a>
                                        `;
                                    notificationList.appendChild(notificationItem);
                                });
                            }
                            // Update badge
                            const badge = toggleButton.querySelector('span');
                            if (data.data.unread_count > 0) {
                                if (badge) {
                                    badge.textContent = data.data.unread_count;
                                } else {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2';
                                    newBadge.textContent = data.data.unread_count;
                                    toggleButton.appendChild(newBadge);
                                }
                            } else if (badge) {
                                badge.remove();
                            }
                        } else {
                            notificationList.innerHTML = '<p class="text-red-500">{{ __("Failed to load notifications") }}</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = '<p class="text-red-500">{{ __("Failed to load notifications") }}</p>';
                    });
            }

            // Initial load
            loadNotifications();
        });
    </script>
@endpush