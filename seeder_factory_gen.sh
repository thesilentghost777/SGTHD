#!/bin/bash
# Script pour générer les seeders et factories pour tous les modèles

# Définition des chemins
folder="/home/ghost/Desktop/hack_the_world/COMPLEXE_TH/SGPTH/SYSTEME_GESTION_TH/SGPTH/app/Models"
index="/home/ghost/Desktop/hack_the_world/COMPLEXE_TH/SGPTH/SYSTEME_GESTION_TH/SGPTH"

# Parcours de tous les fichiers PHP dans le dossier Models
for file in "${folder}"/*.php; do
    if [ -f "$file" ]; then
        # Extraction du nom du fichier sans extension et chemin
        basename=$(basename "$file" .php)
        
        echo "Création du Seeder pour $basename"
        php "${index}/artisan" make:seeder "${basename}Seeder"
        
        echo "Création de la Factory pour $basename"
        php "${index}/artisan" make:factory "${basename}Factory"
    fi
done

echo "madeBy@Ghost"
