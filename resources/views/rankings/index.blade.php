@extends('layouts.app')

@section('content')
<!-- Add this to your layout file or include it in the view -->
<style>
    @media (max-width: 640px) {
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        table {
            display: block;
            max-width: 100%;
        }
    }
</style>
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12 max-w-7xl">
        <!-- Header -->
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-3xl font-bold text-blue-800 mb-2">Employee Performance Rankings</h1>
            <p class="text-gray-600">Monthly performance overview and statistics</p>
        </div>

        <!-- Cards Container -->
        <div class="space-y-8">
            <!-- Regular Employees Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Regular Employees Rankings</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-blue-800 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-blue-800 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-blue-800 uppercase tracking-wider">Average Rating</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($regularEmployees as $index => $employee)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-5">
                                    <span class="bg-blue-100 text-blue-800 font-semibold px-4 py-1 rounded-full">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 font-medium text-gray-900">{{ $employee['name'] }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="text-yellow-500 mr-2">★</div>
                                        <span class="font-medium">{{ number_format($employee['average_rating'], 2) }}</span>
                                        <span class="text-gray-500">/5</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


