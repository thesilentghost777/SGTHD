@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Notifications non lues
                    @if (count($notifications) > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-secondary">
                            Marquer tout comme lu
                        </button>
                    </form>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (count($notifications) > 0)
                        <div class="list-group">
                            @foreach ($notifications as $notification)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $notification->data['subject'] }}</h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] }}</p>
                                    <small>De: {{ $notification->data['sender_name'] }} | {{ $notification->data['created_at'] }}</small>
                                    <div class="mt-2">
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Marquer comme lu
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center mb-0">Aucune notification non lue.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection