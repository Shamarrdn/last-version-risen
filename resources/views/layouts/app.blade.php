<!DOCTYPE html>
<html lang="ar" dir="rtl">

@if(request()->is('products*'))
    {{-- Products pages have their own complete HTML structure --}}
    @yield('content')
@else
    {{-- Regular pages use the standard layout --}}
    <head>
        @include('parts.head')
        @yield('page-meta')
    </head>

    <body class="@yield('body-class')">
        @include('parts.navbar')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        @include('parts.footer')
        @include('parts.scripts')
    </body>
@endif

</html>
