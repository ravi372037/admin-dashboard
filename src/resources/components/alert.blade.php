@if (session('success'))
    <script>Swal.fire('Success', '{{ session('success') }}', 'success');</script>
@endif

@if (session('error'))
    <script>Swal.fire('Error', '{{ session('error') }}', 'error');</script>
@endif
