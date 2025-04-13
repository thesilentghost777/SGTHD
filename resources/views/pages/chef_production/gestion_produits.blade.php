@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Gestion des Produits</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire d'ajout -->
        <form action="{{ route('chef.produits.store') }}" method="POST" class="mb-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="nom" placeholder="Nom du produit" required
                       class="border rounded px-3 py-2" value="{{ old('nom') }}">
                <input type="number" name="prix" placeholder="Prix" required
                       class="border rounded px-3 py-2" value="{{ old('prix') }}">
                <select name="categorie" required class="border rounded px-3 py-2">
                    <option value="">Sélectionner une catégorie</option>
                    <option value="boulangerie" {{ old('categorie') == 'boulangerie' ? 'selected' : '' }}>Boulangerie</option>
                    <option value="patisserie" {{ old('categorie') == 'patisserie' ? 'selected' : '' }}>Pâtisserie</option>
                </select>
                <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Ajouter
                </button>
            </div>
        </form>

        <!-- Liste des produits -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Prix</th>
                        <th class="px-4 py-2">Catégorie</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produits as $produit)
                    <tr class="border-b" data-produit-id="{{ $produit->code_produit }}">
                        <td class="px-4 py-2 nom">{{ $produit->nom }}</td>
                        <td class="px-4 py-2 prix">{{ number_format($produit->prix) }} F</td>
                        <td class="px-4 py-2 categorie">{{ ucfirst($produit->categorie) }}</td>
                        <td class="px-4 py-2">
                            <button onclick="editProduit({{ $produit->code_produit }})"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded mr-2">
                                Modifier
                            </button>
                            <button onclick="confirmDelete({{ $produit->code_produit }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $produits->links() }}
        </div>
    </div>
</div>

<script>
function editProduit(id) {
    const row = document.querySelector(`tr[data-produit-id="${id}"]`);
    const nom = row.querySelector('.nom').textContent;
    const prix = row.querySelector('.prix').textContent.replace(/[^\d]/g, '');
    const categorie = row.querySelector('.categorie').textContent.toLowerCase();

    const form = document.createElement('tr');
    form.innerHTML = `
        <td colspan="4" class="px-4 py-2">
            <form class="flex gap-4" onsubmit="updateProduit(event, ${id})">
                @csrf
                <input type="text" name="nom" value="${nom}" required class="border rounded px-2 py-1">
                <input type="number" name="prix" value="${prix}" required class="border rounded px-2 py-1">
                <select name="categorie" required class="border rounded px-2 py-1">
                    <option value="boulangerie" ${categorie === 'boulangerie' ? 'selected' : ''}>Boulangerie</option>
                    <option value="patisserie" ${categorie === 'patisserie' ? 'selected' : ''}>Pâtisserie</option>
                </select>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Sauvegarder</button>
                <button type="button" onclick="cancelEdit(${id})" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded">Annuler</button>
            </form>
        </td>
    `;

    row.replaceWith(form);
}

async function updateProduit(event, id) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    try {
        const response = await fetch(`/cp/produits/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });

        const data = await response.json();

        if (response.ok) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        alert('Erreur lors de la mise à jour');
        console.error(error);
    }
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(`/cp/produits/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            alert('Erreur lors de la suppression');
            console.error(error);
        });
    }
}

function cancelEdit(id) {
    window.location.reload();
}
</script>
@endsection
