@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">

    @if(isset($error))

        <x-error-message :error="$error" />

    @else

        @include('partials._header')



        <div class="container mx-auto px-4 py-8">

            @if(isset($data))

                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">

                    @include("components.data-types.$dataType", ['data' => $data])

                </div>



                <x-metadata :dataType="$dataType" :data="$data" />

            @else

                <div class="bg-yellow-50 p-4 rounded-lg">

                    <p class="text-yellow-700">Aucune donnée disponible</p>

                </div>

            @endif

        </div>

    @endif

</div>


    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


@endsection
