<div id="network" class="h-96"></div>

<!-- Inclure la bibliothèque vis-network -->
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

<script>
    // Données reçues depuis PHP
    const rawData = {!! json_encode($data) !!};

    // Formatage des données pour vis-network
    const networkData = {
        nodes: [],
        edges: []
    };

    // Exemple de transformation (à adapter selon la structure de $data)
    rawData.forEach((item, index) => {
        // Ajouter un nœud
        networkData.nodes.push({
            id: index + 1, // ID unique
            label: item.label || `Node ${index + 1}` // Libellé du nœud
        });

        // Ajouter une connexion (si applicable)
        if (item.connectedTo) {
            networkData.edges.push({
                from: index + 1, // ID du nœud source
                to: item.connectedTo // ID du nœud cible
            });
        }
    });

    // Initialisation du réseau
    const container = document.getElementById('network');

    if (container && window.vis) {
        new vis.Network(container, networkData, {
            nodes: {
                shape: 'dot',
                size: 16
            },
            physics: {
                stabilization: false
            }
        });
    } else {
        console.error('Erreur : Conteneur #network introuvable ou bibliothèque vis non chargée');
    }
</script>
