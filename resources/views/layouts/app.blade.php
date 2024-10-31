<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ShopRadar - La marketplace du commerce Lyonnais – Cherchez et faites votre shopping dans des milliers de
        magasins à Lyon </title>

    <meta name="description"
        content="ShopRadar est une plateforme qui permet de trouver et comparer les produits disponibles dans les magasins proches de chez vous à Lyon grâce à la géolocalisation. Rejoignez les commerçants de l'agglomération lyonnaise !">

    <meta name="keywords" content="ShopRadar, géolocalisation, shopping, Lyon, comparaison prix, commerçants lyonnais">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKH-dY6c4izFPgPDuNVRI6UiFUcN0LWpU&libraries=places,marker">
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div>




        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
