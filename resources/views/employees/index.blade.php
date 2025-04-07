<!-- resources/views/employees/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Liste des Employés</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($employees as $employee)
                        <a href="{{ route('employees.show', $employee) }}"
                           class="block group">
                            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow duration-300 ease-in-out">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>
                                            {{ $employee->name }}
                                        </h3>
                                        <p class="text-gray-600">{{ $employee->age }} ans</p>
                                        @php
                                            $latestEvaluation = $employee->evaluation->first(); // Get the latest evaluation, or null if none
                                        @endphp
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $latestEvaluation && $latestEvaluation->note >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                Note: {{ $latestEvaluation ? $latestEvaluation->note : 0 }}/20
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
