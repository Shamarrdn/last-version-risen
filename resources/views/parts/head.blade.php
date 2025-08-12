<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'RISEN - Born in KSA')</title>

<!-- Bootstrap 5.3 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Base Styles -->
<link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/style.css') }}?t={{ time() }}">

<!-- Products Pages CSS -->
@if(request()->is('products*'))
<link rel="stylesheet" href="{{ asset('assets/css/customer/products.css') }}?t={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/customer/products-show.css') }}?t={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/customer/custom-black-theme.css') }}?t={{ time() }}">
<link rel="stylesheet" href="{{ asset('assets/kids/css/common.css') }}?t={{ time() }}">
@endif

<!-- Page Specific Styles -->
@yield('page-css')
