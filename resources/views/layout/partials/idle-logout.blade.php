<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    // Idle time in seconds
    const maxIdle = {{ env('SESSION_LIFETIME', 15) }} * 60; // default to 15 minutes
    const warningTime = 60; // 1 minute before redirect to show popup
    let idleTime = 0;
    let idleInterval;

    function resetIdle() {
        idleTime = 0;
    }

    // Track user activity
    ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(evt => {
        document.addEventListener(evt, resetIdle, false);
    });

    // Check every second
    idleInterval = setInterval(() => {
        idleTime++;
        if(idleTime === maxIdle - warningTime) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Timeout Soon',
                text: `You will be logged out in ${warningTime} seconds due to inactivity.`,
                timer: warningTime * 1000,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
            });
        } else if(idleTime >= maxIdle) {
            window.location.href = "{{ route('logout') }}";
        }
    }, 1000);
})();
</script>
