<div class="card p-4">
    <h2 class="mb-4">Buscador de Productos</h2>
    <?php renderSearchFilters($departments, $products); ?>
    <hr>
    <h3>Resultados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th>Número</th>
                <th>Ubicación</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($filtered_data) > 0): ?>
                <?php foreach ($filtered_data as $entry): ?>
                    <tr>
                        <td><?php echo $entry['department']; ?></td>
                        <td><?php echo $entry['municipality']; ?></td>
                        <td><?php echo $entry['product']; ?></td>
                        <td><?php echo $entry['proveedor']; ?></td>
                        <td><?php echo $entry['numero']; ?></td>
                        <td><a href="<?php echo htmlspecialchars($entry['ubicacion']); ?>" target="_blank"><?php echo htmlspecialchars($entry['ubicacion']); ?></a></td>
                        <td><?php echo $entry['cantidad'] . ' ' . $entry['unidad']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No hay datos disponibles.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>