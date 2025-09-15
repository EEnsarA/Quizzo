<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    
    
</head>

<body>      
    <div x-data class="flex min-h-screen">
        <x-sidebar/>
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class = "$store.sidebar.open ? 'ml-64' : 'ml-16'"
        >
            <div class="flex-1 pb-8">
                <x-navbar />
                <div class="container mx-auto">
                    <div class="content">
                        @yield("content")
                    </div>
                </div>
            </div>
            <x-footer />
        </div>
    </div>
</body>

    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
</html>