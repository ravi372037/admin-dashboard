<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Admin Dashboard</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/admin/' . config('admin.theme')) }}/css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/' . config('admin.theme')) }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/' . config('admin.theme')) }}/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/' . config('admin.theme')) }}/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            @includeWhen(Auth::check(), 'admin::partials.navbar')
            @includeWhen(Auth::check(), 'admin::partials.sidebar')
            <div class="content">
                @yield('content')
            </div>
            @includeWhen(Auth::check(), 'admin::partials.footer')
        </div>
    </div>
    <!-- General JS Scripts -->
    <script src="{{ asset('assets/admin/' . config('admin.theme')) }}/js/app.min.js"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('assets/admin/' . config('admin.theme')) }}/bundles/apexcharts/apexcharts.min.js"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('assets/admin/' . config('admin.theme')) }}/js/page/index.js"></script>
    <!-- Template JS File -->
    <script src="{{ asset('assets/admin/' . config('admin.theme')) }}/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('assets/admin/' . config('admin.theme')) }}/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

    </script>
    @if(session('success'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "{{ session('success') }}"
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "error",
                title: "{{ session('error') }}"
            });
        </script>
    @endif
    @if(session('warning'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "warning",
                title: "{{ session('warning') }}"
            });
        </script>
    @endif
    @if(session('info'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "info",
                title: "{{ session('info') }}"
            });
        </script>
    @endif

</body>

</html>