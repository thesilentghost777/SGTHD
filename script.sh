#!/bin/bash
# Script pour un remplissage rapide de pages Laravel
echo "ghost_entity_generator"
echo "définition des chemins"

# Définition des chemins
folder="/home/ghost/Desktop/hack_the_world/COMPLEXE_TH/SGPTH/SYSTEME_GESTION_TH/SGPTH/resources/views/${1}"
folder_name="${1}"

# Vérification si le paramètre est fourni
if [ -z "${1}" ]; then
    echo "Erreur: Veuillez fournir un nom de dossier en paramètre"
    exit 1
fi

# Création du dossier s'il n'existe pas
mkdir -p "${folder}"

echo "création du fichier default.blade.php"
touch "${folder}/${folder_name}/${folder_name}_default.blade.php"

echo "création du fichier dashboard.blade.php"
touch "${folder}/${folder_name}/${folder_name}_dashboard.blade.php"

# Création des variables pour @include et @vite
inc="@include('pages/${folder_name}/${folder_name}_default')"
vite="@vite(['resources/css/${folder_name}/${folder_name}_default.css','resources/js/${folder_name}/${folder_name}_default.js'])"
vite2="@vite(['resources/css/${folder_name}/${folder_name}_dashboard.css','resources/js/${folder_name}/${folder_name}_dashboard.js'])"

echo "Écriture des fichiers"
echo "${inc}" > "${folder}/${folder_name}_dashboard.blade.php"
echo "${vite}" > "${folder}/${folder_name}_default.blade.php"
echo "${vite2}" >> "${folder}/${folder_name}_dashboard.blade.php"


echo "Création des fichiers CSS"
touch "${folder}/../css/${folder_name}/${folder_name}_default.css"
touch "${folder}/../css/${folder_name}/${folder_name}_dashboard.css"

echo  "Création des fichiers JS"
touch "${folder}/../js/${folder_name}/${folder_name}_default.js"
touch "${folder}/../js/${folder_name}/${folder_name}_dashboard.js"

echo "Définition des sources"
sources="/home/ghost/Desktop/hack_the_world/COMPLEXE_TH/SGPTH/SYSTEME_GESTION_TH/SGPTH"

echo "création du controller"
php "${sources}/artisan" make:controller "${folder_name^}Controller"


echo "ajout de la route"

route_content="use App\Http\Controllers\\${folder_name^}Controller;
Route::get('${folder_name}/dashboard', [${folder_name^}Controller::class, 'dashboard'])->name('${folder_name}-dashboard');"

echo "${route_content}" >> "${sources}/routes/web.php"

echo "Fin du Programme By @ghost Creer juste le contenu du controlleur maintenant"
