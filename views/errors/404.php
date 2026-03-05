<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Court Reservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Browser Icon -->
    <link rel="icon" href="/app/images/sports-svgrepo-com.svg" type="image/svg+xml">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ph-blue': '#0038a8',
                        'ph-red': '#ce1126',
                        'ph-yellow': '#fcd116'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <i class="fas fa-basketball-ball text-ph-blue text-8xl animate-bounce"></i>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Court Not Found</h2>
        <p class="text-gray-500 mb-8">Oops! Looks like this court doesn't exist or has been moved.</p>
        <div class="flex justify-center space-x-4">
            <a href="/Court-Reservation/" class="bg-ph-blue text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-home mr-2"></i>Go Home
            </a>
            <a href="/Court-Reservation/courts" class="border border-ph-blue text-ph-blue px-6 py-3 rounded-lg hover:bg-blue-50 transition">
                <i class="fas fa-search mr-2"></i>Browse Courts
            </a>
        </div>
    </div>
</body>
</html>
