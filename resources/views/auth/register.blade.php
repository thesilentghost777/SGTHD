@vite(['resources/css/register.css', 'resources/js/register.js'])
<x-guest-layout>
    <head>
        <style>
            input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #dadce0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s;
}
        </style>
    </head>
    <div id="form">
    <form method="POST" action="{{ route('sign_up') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom')"/>
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- numero de telephone -->
         <div class="mt-4">
            <x-input-label for="num_tel" :value="__('Telephone')" />
            <x-text-input id="telephone" class="block mt-1 w-full" type="tel" name="num_tel" :value="old('telephone')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        <!-- date_naissance -->
        <div class="mt-4">
            <x-input-label for="num_tel" :value="__('Date de Naissance')" />
            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date_naissance" :value="old('date_naissance')" required autocomplete="date" />
            <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
        </div>

        <!-- annee debut service-->
        <div class="mt-4">
            <x-input-label for="annee_debut_service" :value="__('Annee de debut du service')" />
            <x-text-input id="annee_debut_service" class="block mt-1 w-full" type="number" name="annee_debut_service" :value="old('annee_debut_service')" required autocomplete="date" />
            <x-input-error :messages="$errors->get('annee_debut_service')" class="mt-2" />
        </div>

        <div class="mt-4">
        <x-input-label for="secteur" :value="__('Département')"/>
        <select id="secteur" name="secteur">
            <option value="">Sélectionnez un département</option>
            <option value="alimentation">Alimentation</option>
            <option value="production">Production</option>
            <option value="glace">Glace</option>
            <option value="administration">Administration</option>
        </select>
        </div><br>

        <div class="form-group" class="t-4">
        <x-input-label for="role" :value="__('Role')"/>
        <select id="role" name="role" required>
            <option value="">Sélectionnez un rôle</option>
            <optgroup label="Alimentation">
                <option value="caissiere" data-department="alimentation">Caissier(e)</option>
                <option value="cave" data-department="alimentation">Calviste</option>
                <option value="rayon" data-department="alimentation">Rayoniste</option>
                <option value="controlleur" data-department="alimentation">Contrôleur</option>
                <option value="tech_surf" data-department="alimentation">Technicien de Surface</option>
            </optgroup>
            <optgroup label="Production">
                <option value="patissier" data-department="production">Patissier(e)</option>
                <option value="boulanger" data-department="production">Boulanger(e)</option>
                <option value="pointeur" data-department="production">Pointeur</option>
                <option value="enfourneur" data-department="production">Enfourneur</option>
                <option value="tech_surf" data-department="production">Technicien de Surface</option>
            </optgroup>
            <optgroup label="Glace">
                <option value="glaciere" data-department="glace">Glacière</option>
            </optgroup>
            <optgroup label="Administration">
                <option value="chef_production" data-department="administration">Chef Production</option>
                <option value="dg" data-department="administration">DG</option>
                <option value="ddg" data-department="administration">DDG</option>
                <option value="pdg" data-department="administration">PDG</option>
            </optgroup>
        </select>
        <div class="error-message" id="roleError"></div>
    </div>

        <!--code secret du poste -->
        <div class="mt-4">
            <x-input-label for="code_secret" :value="__('Code secret du poste')" />

            <x-text-input id="code_secret" class="block mt-1 w-full"
                            type="number"
                            name="code_secret"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmation de Mot de passe')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const secteurSelect = document.getElementById('secteur');
            const roleSelect = document.getElementById('role');
            
            // Fonction pour filtrer les rôles en fonction du département
            function filterRoles() {
                const selectedDepartment = secteurSelect.value;
                const options = roleSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    const department = option.getAttribute('data-department');
                    if (!department || department === selectedDepartment) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Réinitialiser la sélection si le rôle actuel n'appartient pas au département
                const currentRole = roleSelect.value;
                const currentOption = roleSelect.querySelector(`option[value="${currentRole}"]`);
                if (currentOption && currentOption.style.display === 'none') {
                    roleSelect.value = '';
                }
            }
            
            secteurSelect.addEventListener('change', filterRoles);
            filterRoles(); // Appliquer le filtre au chargement
        });
        

        </script>
</x-guest-layout>
