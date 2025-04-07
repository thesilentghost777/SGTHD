<!-- resources/views/history/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Historique des actions</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Utilisateur</th>
                                <th>Type d'action</th>
                                <th>Adresse IP</th>
                                @can('delete-history')
                                <th>Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $history->description }}</td>
                                <td>{{ $history->user ? $history->user->name : 'Système' }}</td>
                                <td>{{ $history->action_type }}</td>
                                <td>{{ $history->ip_address }}</td>
                                @can('delete-history')
                                <td>
                                    <form action="{{ route('history.destroy', $history) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center">
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection