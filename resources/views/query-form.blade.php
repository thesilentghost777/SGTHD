@extends('layouts.app')

@section('content')
<br><br>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white">
                    <h3>Sherlock</h3>
                    <h5>  Posez votre question je vous repondrais</h5>
                </div>

                <div class="card-body bg-light">
                    <form id="query-form" action="{{ route('process.query') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="query" class="font-weight-bold text-primary">Votre question (soyez tres precis et bref)</label>
                            <textarea
                                class="form-control"
                                id="query"
                                name="query"
                                rows="4"
                                placeholder="Exemple : Combien de baguettes ont été vendues le 1er janvier 2024 ?"
                                required
                            ></textarea>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success btn-block">
                                Soumettre la requête
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h5 class="font-weight-bold">Exemples de questions :</h5>
                        <ul class="text-muted">
                            <li>Combien de baguettes ont été vendues le 1er janvier 2024 ?</li>
                            <li>Quel est le produit le plus rentable ce mois-ci ?</li>
                            <li>Montre-moi les productions totales d'hier.</li>
                        </ul>
                    </div>

                    <div class="alert alert-info mt-4" role="alert">
                        <strong>Important :</strong> Il est possible que certaines requêtes ne fonctionnent pas correctement en raison des limitations de l'IA ou d'un manque de ressources (tokens). Si vous rencontrez un problème, essayez de reformuler votre question ou contactez le concepteur de l'App
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('query-form').addEventListener('submit', function(e) {
    // Ajout d'un petit loader pour l'UX
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.innerHTML = 'Chargement...';
    submitButton.disabled = true;
});
</script>
@endsection
