@props(['url'])

<div class="bg-white p-6 rounded-lg shadow mt-6 relative">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>

    <ul id="activityFeed" class="space-y-4 text-sm text-gray-700 max-h-64 overflow-y-auto pr-2 transition-all duration-300">
        <li class="text-gray-400">Loading...</li>
    </ul>

    <div id="lastUpdated" class="absolute bottom-2 right-4 text-xs text-gray-400 italic">
        Last updated just now
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const feedEl = document.getElementById('activityFeed');
        const updatedEl = document.getElementById('lastUpdated');
        const apiUrl = "{{ $url }}";

        let lastUpdatedTime = new Date();

        function updateTimestamp() {
            const now = new Date();
            const secondsAgo = Math.floor((now - lastUpdatedTime) / 1000);
            updatedEl.textContent = `Last updated ${secondsAgo} sec ago`;
        }

        async function loadActivity() {
            try {
                const res = await fetch(apiUrl);
                const data = await res.json();

                // Animate fade-out before update
                feedEl.classList.add('opacity-0');
                setTimeout(() => {
                    feedEl.innerHTML = '';

                    if (!data || data.length === 0) {
                        feedEl.innerHTML = '<li class="text-gray-400">No recent activity</li>';
                    } else {
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.classList.add("flex", "items-start", "space-x-3", "transition-all", "duration-300", "opacity-0");
                            li.classList.add("transition", "transform", "duration-300", "ease-out", "hover:scale-[1.02]");


                            let icon = '📝';
                            let color = 'text-blue-500';
                            if (item.type === "order") {
                                icon = '🛒';
                                color = 'text-green-500';
                            } else if (item.type === "report") {
                                icon = '📄';
                                color = 'text-red-500';
                            }

                            li.innerHTML = `
                                <div class="text-xl ${color}">${icon}</div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">${item.title}</div>
                                    <div class="text-xs text-gray-500">
                                        <span class="capitalize">${item.type}</span> by User #${item.user_id} · ${item.time}
                                    </div>
                                </div>
                            `;

                            feedEl.appendChild(li);

                            // Animate fade-in
                            requestAnimationFrame(() => {
                                li.classList.remove("opacity-0");
                                li.classList.add("opacity-100");
                            });
                        });
                    }

                    // Animate fade-in for whole list
                    feedEl.classList.remove('opacity-0');
                    feedEl.classList.add('opacity-100');

                    // Update "last updated" time
                    lastUpdatedTime = new Date();
                    updateTimestamp();
                }, 200);
            } catch (error) {
                console.error(error);
                feedEl.innerHTML = '<li class="text-red-400">Failed to load activity</li>';
            }
        }

        // Initial load
        loadActivity();

        // Refresh every 30 seconds
        setInterval(loadActivity, 30000);

        // Update "X sec ago" text every second
        setInterval(updateTimestamp, 1000);
    });
</script>
