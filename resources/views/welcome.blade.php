<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" referrerpolicy="no-referrer" />
        <!--Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800;900&display=swap">
        <title>RocketMGA Onboarding</title>
        @vite('resources/css/app.css')
        <script async defer 
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDP7Mf0mNj6uEdgdVdjNQndu6yCNJe2J74&libraries=places&callback=initMap">
        </script>
    </head>
    <body>
        <div id="app"></div>
        @vite('resources/js/app.js')
    </body>
</html>
