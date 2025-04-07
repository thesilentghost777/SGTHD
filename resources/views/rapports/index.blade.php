@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
        <h1 class="text-3xl font-bold text-white">Rapports des Employés</h1>
        <p class="text-blue-100 mt-2">Générez des rapports professionnels pour chaque employé</p>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Sélectionnez un employé pour générer son rapport</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                @forelse($employees as $employee)
                    <a href="{{ route('rapports.generer', $employee->id) }}" class="block">
                        <div class="bg-gradient-to-r from-white to-blue-50 border border-blue-100 rounded-lg shadow-sm p-4 hover:shadow-md transition duration-200 hover:border-blue-300">
                            <div class="flex items-center space-x-4">
                                <div class="rounded-full bg-blue-600 h-10 w-10 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-800">{{ $employee->name }}</h4>
                                    <p class="text-sm text-gray-600">
                                        <span class="capitalize">{{ $employee->role ?? 'Non défini' }}</span>
                                        @if($employee->secteur)
                                            - {{ $employee->secteur }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 p-4 bg-yellow-50 rounded-lg text-yellow-700">
                        Aucun employé n'a été trouvé dans le système.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
