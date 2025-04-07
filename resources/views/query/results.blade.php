@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-700 leading-tight border-b-2 border-blue-300 pb-2">
            {{ __('Résultats pour ') . $tableName }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-blue-800 border-b pb-2">Informations sur la requête</h3>
                    <pre class="mt-2 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-md overflow-x-auto text-sm text-gray-700">{{ $message }}</pre>
                </div>


                <div class="overflow-x-auto">
                    @if(!empty($results))
                        <table class="min-w-full divide-y divide-gray-300 border rounded-lg overflow-hidden">
                            <thead class="bg-blue-100 text-blue-800">
                                <tr>
                                    @foreach((array)$results[0] as $column => $value)
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border-b border-blue-300">
                                            {{ $column }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($results as $row)
                                    <tr class="hover:bg-gray-100">
                                        @foreach((array)$row as $value)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-b">
                                                {{ $value }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucun résultat trouvé</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
