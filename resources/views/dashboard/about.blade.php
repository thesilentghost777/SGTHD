@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Boulangerie Pâtisserie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="relative h-80">
                    <div class="absolute inset-0">
                        <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Boulangerie">
                        <div class="absolute inset-0 bg-blue-600 mix-blend-multiply"></div>
                    </div>
                    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">À propos de nous</h1>
                        <p class="mt-6 text-xl text-blue-100 max-w-3xl">Découvrez l'histoire et les valeurs qui font de notre boulangerie-pâtisserie un lieu unique.</p>
                    </div>
                </div>

                <div class="px-4 py-16 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        <div class="space-y-12">
                            <div class="space-y-5 sm:space-y-4">
                                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Notre Histoire</h2>
                                <p class="text-xl text-gray-500">Depuis plus de 30 ans, nous perpétuons la tradition de la boulangerie artisanale camerounaise tout en innovant constamment pour satisfaire les goûts de nos clients.</p>
                            </div>

                            <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <div class="space-y-4">
                                        <div class="aspect-w-3 aspect-h-2">
                                            <img class="object-cover shadow-lg rounded-lg" src="{{ asset('assets/illustrations/mohamed-hassouna-N4gtuEZ5gWc-unsplash.jpg') }}">
                                        </div>
                                        <div class="space-y-2">
                                            <div class="text-lg leading-6 font-medium space-y-1">
                                                <h3>Notre Savoir-faire</h3>
                                                <p class="text-blue-600">Tradition & Innovation</p>
                                            </div>
                                            <div class="text-lg">
                                                <p class="text-gray-500">Un mélange parfait entre techniques traditionnelles et innovations modernes.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="space-y-4">
                                        <div class="aspect-w-3 aspect-h-2">
                                            <img class="object-cover shadow-lg rounded-lg" src="{{ asset('assets/illustrations/quality.jpeg') }}" alt="Valeurs">
                                        </div>
                                        <div class="space-y-2">
                                            <div class="text-lg leading-6 font-medium space-y-1">
                                                <h3>Nos Valeurs</h3>
                                                <p class="text-blue-600">Qualité & Authenticité</p>
                                            </div>
                                            <div class="text-lg">
                                                <p class="text-gray-500">L'engagement pour la qualité et l'authenticité guide chacune de nos créations.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="space-y-4">
                                        <div class="aspect-w-3 aspect-h-2">
                                            <img class="object-cover shadow-lg rounded-lg" src="{{ asset('assets/illustrations/team.jpg') }}" alt="Équipe">
                                        </div>
                                        <div class="space-y-2">
                                            <div class="text-lg leading-6 font-medium space-y-1">
                                                <h3>Notre Équipe</h3>
                                                <p class="text-blue-600">Passion & Expertise</p>
                                            </div>
                                            <div class="text-lg">
                                                <p class="text-gray-500">Des artisans passionnés qui mettent leur expertise au service de la qualité.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <a href="#" style="color: blue">Visitez notre site web</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection
