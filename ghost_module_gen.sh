#!/bin/bash

# Script pour un remplissage rapide de pages Laravel
echo "ghost_entity_generator"
echo "définition des chemins"

if [ $# != 2 ]; then
    echo "utilisation:: ghost_module__gen folder_name option_name  (ex:: ghost_module_gen producteur reserverMp)"
    exit 1
fi

# Définition des chemins de base
BASE_PATH="/home/ghost/Desktop/hack_the_world/COMPLEXE_TH/SGPTH/SYSTEME_GESTION_TH/SGPTH"
RESOURCES_PATH="${BASE_PATH}/resources"
folder="${RESOURCES_PATH}/views/pages/${1}"
folder_name="${1}"
option_name="${2}"
name="${folder_name}-${option_name}"

# Création des dossiers s'ils n'existent pas
mkdir -p "${folder}"
mkdir -p "${RESOURCES_PATH}/css/${folder_name}"
mkdir -p "${RESOURCES_PATH}/js/${folder_name}"

echo "création du fichier ${name}.blade.php"
if touch "${folder}/${name}.blade.php"; then
    echo "✓ Fichier blade créé avec succès"
else
    echo "✗ Erreur lors de la création du fichier blade"
    exit 1
fi

echo "Création du fichier CSS associé"
if touch "${RESOURCES_PATH}/css/${folder_name}/${name}.css"; then
    echo "✓ Fichier CSS créé avec succès"
else
    echo "✗ Erreur lors de la création du fichier CSS"
    exit 1
fi

echo "Création du fichier JS associé"
if touch "${RESOURCES_PATH}/js/${folder_name}/${name}.js"; then
    echo "✓ Fichier JS créé avec succès"
else
    echo "✗ Erreur lors de la création du fichier JS"
    exit 1
fi

# Création des variables pour @include et @vite
vite="@vite(['resources/css/${folder_name}/${name}.css','resources/js/${folder_name}/${name}.js'])"

echo "liaison du fichier blade et css,js"
echo "${vite}" > "${folder}/${name}.blade.php"

echo "Création du controller s'il n'existe pas"
CONTROLLER_PATH="${BASE_PATH}/app/Http/Controllers/${folder_name^}Controller.php"

if [ ! -f "$CONTROLLER_PATH" ]; then
    echo "Création du controller..."
    php "${BASE_PATH}/artisan" make:controller "${folder_name^}Controller"
else
    echo "Le controller existe déjà"
fi

echo "Ajout de la route"
route_content="Route::get('${folder_name}/${option_name}', [${folder_name^}Controller::class, '${option_name}'])->name('${folder_name}-${option_name}');"

# Ajoute la route avec une ligne vide pour la lisibilité
echo -e "\n${route_content}" >> "${BASE_PATH}/routes/web.php"

echo "✓ Programme terminé avec succès"
echo "N'oubliez pas de créer la méthode ${option_name} dans le controller ${folder_name^}Controller"
