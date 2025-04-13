<div class="h-96" id="tree"></div>

<style>
    .link {
        fill: none;
        stroke: #ccc;
        stroke-width: 2px;
    }

    .node circle {
        fill: #fff;
        stroke: #4299e1;
        stroke-width: 2px;
    }

    .node text {
        font-family: sans-serif;
        font-size: 12px;
        fill: #2c5282;
    }
</style>

<!-- Ajouter D3.js depuis CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données de l'arbre
        const treeData = {!! json_encode($data) !!};

        // Configuration des dimensions
        const container = document.getElementById('tree');
        const width = container.offsetWidth;
        const height = container.offsetHeight;
        const margin = { top: 20, right: 20, bottom: 20, left: 20 };
        const innerWidth = width - margin.left - margin.right;
        const innerHeight = height - margin.top - margin.bottom;

        // Création du SVG
        const svg = d3.select('#tree')
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        // Configuration de l'arbre
        const tree = d3.tree()
            .size([innerWidth, innerHeight]);

        // Préparation des données
        const root = d3.hierarchy(treeData[0]); // Utilisation du premier objet si vous avez plusieurs arbres
        const nodes = tree(root);

        // Création des liens
        const links = svg.selectAll('.link')
            .data(nodes.links())
            .enter()
            .append('path')
            .attr('class', 'link')
            .attr('d', d3.linkVertical()
                .x(d => d.x)
                .y(d => d.y));

        // Création des nœuds
        const node = svg.selectAll('.node')
            .data(nodes.descendants())
            .enter()
            .append('g')
            .attr('class', 'node')
            .attr('transform', d => `translate(${d.x},${d.y})`);

        // Ajout des cercles aux nœuds
        node.append('circle')
            .attr('r', 5)
            .attr('fill', d => d.children ? '#4299e1' : '#48bb78')
            .attr('stroke-width', 2)
            .attr('stroke', '#fff');

        // Ajout des étiquettes aux nœuds
        node.append('text')
            .attr('dy', '.35em')
            .attr('y', d => d.children ? -12 : 12)
            .attr('text-anchor', 'middle')
            .text(d => d.data.name)
            .attr('fill', '#2d3748');

        // Ajout de l'interactivité
        node.on('mouseover', function() {
            d3.select(this)
                .select('circle')
                .transition()
                .duration(200)
                .attr('r', 8)
                .attr('fill', '#667eea');
        })
        .on('mouseout', function() {
            d3.select(this)
                .select('circle')
                .transition()
                .duration(200)
                .attr('r', 5)
                .attr('fill', d => d.children ? '#4299e1' : '#48bb78');
        });

        // Ajout du zoom
        const zoom = d3.zoom()
            .scaleExtent([0.5, 2])
            .on('zoom', (event) => {
                svg.attr('transform', event.transform);
            });

        d3.select('#tree svg')
            .call(zoom);
    });
</script>
