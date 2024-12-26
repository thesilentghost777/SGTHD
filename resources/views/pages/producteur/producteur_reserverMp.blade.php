@vite(['resources/css/producteur/producteur-reserverMp.css','resources/js/producteur/producteur-reserverMp.js'])
@include('pages/producteur/pdefault')
simple :: le chef de production va enregistrer toutes les matieres premiere donner a un producteur
            le producteur va demander une reservation de matiere premiere   
            le cp va valider et la mp sera automatiquement enregistrer commme matiere premiere donner au producteur pour la journe suivant
            toutes matiere premiere devra avoir un prix
            il doit avoir une table matiere_fixes qui specifie toutes les matiere premiere avec leur prix() .. a chaqu assignation ou reservation de mp
            on verifiera toujours que cette mp est dans la base de donner:: chaque mp aura un prix :: cad que meme si la mp est acheter en sac
            (farine) , elle sera toujours detailler lors de l'Utilisation pour maximiser les benefices reels .. Les calculs des manquants en suivra facilement
            