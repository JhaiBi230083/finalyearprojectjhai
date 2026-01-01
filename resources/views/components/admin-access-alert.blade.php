@if(session()->has('admin_access_denied'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Filament notification system is available
            if (window.Filament) {
                // Use Filament's notification system
                window.Filament.notify('error', '{{ session("admin_access_denied") }}');
            } else {
                // Fallback to browser alert
                alert('{{ session("admin_access_denied") }}');
            }

            // Remove the session flash message so it doesn't show again on refresh
            fetch('{{ route("filament.admin.auth.logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            }).then(() => {
                window.location.href = '{{ route("filament.admin.auth.login") }}';
            });
        });
    </script>
@endif
