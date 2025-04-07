<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rapport - {{ $title ?? 'TH MARKET' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation Bar (sera masqué à l'impression) -->
        <nav class="bg-white shadow-sm py-4 print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">TH MARKET</a>
                    <div class="flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">Tableau de bord</a>
                        <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <div id="report-content" class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- En-tête du rapport (apparaîtra à l'impression) -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-6 print:shadow-none print:mb-8">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4 print:border-b-2">
                        <div class="flex items-center">
                            <div class="mr-4 print:mr-6">
                                <h1 class="text-2xl font-bold text-gray-800 print:text-3xl">TH MARKET</h1>
                                <p class="text-gray-500">Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if(isset($description))
                    <div class="text-gray-700 print:text-gray-800">
                        {{ $description }}
                    </div>
                    @endif
                </div>

                <!-- Contenu du rapport -->
                <div class="bg-white shadow-md rounded-lg p-6 print:shadow-none print:p-0">
                    @yield('content')

                    <!-- Pied de page -->
                    <div class="mt-8 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm print:fixed print:bottom-0 print:left-0 print:w-full print:border-t-2">
                        <p>© {{ date('Y') }} Easy Gest - Tous droits réservés</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function printReport() {
        window.print();
    }
    </script>
</body>
</html>
