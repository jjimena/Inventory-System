<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title')
    </title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <style>
        body {
            background-image: url('/images/LPG.jpg');
            /* Path to your background image */
            background-size: cover;
            /* Cover the entire body area */
            background-position: center;
            /* Center the background image */
            height: 100vh;
            /* Full viewport height */
            margin: 0;
            /* Remove default margin */
            display: flex;
            /* Flex container */
            justify-content: center;
            /* Center children horizontally */
            align-items: center;
            /* Center children vertically */
        }

        .form-signin {
            background-color: rgba(255, 255, 255, 0.8);
            /* Semi-transparent white background for the form */
            padding: 20px;
            /* Padding inside the form */
            border-radius: 5px;
            /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            /* Soft shadow for depth */
        }

        h1 {
            color: #333;
            /* Darker text for headings */
            font-weight: bold;
            /* Bold font weight */
        }

        button.btn-primary {
            background-color: ##0d6efd;
            /* Tomato red for the submit button */
            border-color: ##0d6efd;
            /* Orange border */
        }

        button.btn-primary:hover {
            background-color: #ff4500;
            /* Change hover state to orange */
            border-color: #ff6347;
            /* Change hover state border to tomato red */
        }
    </style>



</head>

<body>
    <main class="container d-flex align-items-center justify-content-center" style="height: 100vh;">
        @yield('content')
    </main>
</body>

</html>
