<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

<!-- Additional Libraries for Products Pages -->
@if(request()->is('products*'))
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endif

<!-- Base JavaScript -->
<script src="{{ asset('assets/js/index.js') }}?t={{ time() }}"></script>

<!-- Products Pages JavaScript -->
@if(request()->is('products*'))
<script>
    window.appConfig = {
        routes: {
            products: {
                filter: '{{ route("products.filter") }}',
                details: '{{ route("products.details", ["product" => "__id__"]) }}'
            }
        }
    };
</script>
<script src="{{ asset('assets/js/customer/products.js') }}?t={{ time() }}"></script>
@endif

<!-- Page Specific Scripts -->
@yield('page-scripts')
