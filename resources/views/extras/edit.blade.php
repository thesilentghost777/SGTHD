{{-- resources/views/extras/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier Extra</h1>

    <form action="{{ route('extras.update', $extra) }}" method="POST">
        @csrf
        @method('PUT')
        @include('extras.form')

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
