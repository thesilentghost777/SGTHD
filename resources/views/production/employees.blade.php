@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">

    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">

        <h1 class="text-3xl font-bold text-white">Employés de Production</h1>

        <p class="text-blue-100 mt-2">Liste des boulangers et pâtissiers</p>

    </div>

    <div class="container mx-auto px-4 py-8">

        <div class="bg-white rounded-lg shadow-md overflow-hidden">

            <div class="p-6 border-b border-gray-200">

                <h3 class="text-xl font-semibold text-gray-800">Sélectionnez un employé pour voir ses détails</h3>

            </div>



            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">

                @foreach($employees as $employee)

                <a href="{{ route('employee.details2', $employee->id) }}" class="block">

                    <div class="bg-gradient-to-r from-white to-blue-50 border border-blue-100 rounded-lg shadow-sm p-4 hover:shadow-md transition duration-200 hover:border-blue-300">

                        <div class="flex items-center space-x-4">

                            <div class="rounded-full bg-blue-600 h-10 w-10 flex items-center justify-center text-white font-semibold">

                                {{ strtoupper(substr($employee->name, 0, 1)) }}

                            </div>

                            <div>

                                <h4 class="text-lg font-medium text-gray-800">{{ $employee->name }}</h4>

                                <p class="text-sm text-gray-600 capitalize">{{ $employee->role }}</p>

                            </div>

                        </div>

                    </div>

                </a>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection
