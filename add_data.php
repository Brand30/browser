<?php
$data = $pdo->query("SELECT ud.id, d.id as department_id, d.name as department, m.id as municipality_id, m.name as municipality, p.id as product_id, p.name as product, 
                     ud.added_by, ud.proveedor, ud.numero, ud.ubicacion, ud.cantidad, ud.unidad 
                     FROM user_data ud 
                     JOIN departments d ON ud.department_id = d.id 
                     JOIN municipalities m ON ud.municipality_id = m.id 
                     JOIN products p ON ud.product_id = p.id")->fetchAll(PDO::FETCH_ASSOC);

// Filtrar datos para Técnicos: solo sus propias entradas
$filtered_data = $_SESSION['role'] === "Técnico" ? array_filter($data, function($entry) {
    return $entry['added_by'] == $_SESSION['user_id'];
}) : $data;
?>
<div class="card p-4">
    <h2 class="mb-4">Agregar Datos</h2>
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Departamento</label>
                <select name="department" id="department-add" class="form-select" required onchange="updateMunicipalitiesAdd(this)">
                    <option value="">Seleccione</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Municipio</label>
                <select name="municipality" id="municipality-add" class="form-select" required disabled>
                    <option value="">Seleccione primero un departamento</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Proveedor</label>
                <input type="text" name="proveedor" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ubicación</label>
                <input type="text" name="ubicacion" class="form-control" required placeholder="Ej: https://maps.google.com/...">
            </div>
        </div>
        <div id="products-container">
            <h4>Productos</h4>
            <div class="row mb-3 product-row">
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="products[]" class="form-select">
                        <option value="">Seleccione</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cantidad (opcional)</label>
                    <input type="number" step="0.01" name="quantities[]" class="form-control" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unidad</label>
                    <select name="units[]" class="form-select">
                        <option value="kg">kg</option>
                        <option value="lb">lb</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addProductRow()">Agregar otro producto</button>
        <br>
        <button type="submit" name="add_data" class="btn btn-primary">Agregar</button>
    </form>
    <hr>
    <h3>Datos Agregados</h3>
    <?php if (count($filtered_data) > 0): ?>
        <button type="button" class="btn btn-warning mb-3" onclick='editAll(<?php echo json_encode(array_values($filtered_data)); ?>)'>Editar Todos</button>
        <a href="?vista=add_data&delete_all=true" class="btn btn-danger mb-3" onclick="return confirm('¿Está seguro de que desea eliminar todos los datos?')">Eliminar Todos</a>
    <?php endif; ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th>Número</th>
                <th>Ubicación</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $entry): ?>
                    <tr>
                        <td><?php echo $entry['id']; ?></td>
                        <td><?php echo $entry['department']; ?></td>
                        <td><?php echo $entry['municipality']; ?></td>
                        <td><?php echo $entry['product']; ?></td>
                        <td><?php echo $entry['proveedor']; ?></td>
                        <td><?php echo $entry['numero']; ?></td>
                        <td><a href="<?php echo htmlspecialchars($entry['ubicacion']); ?>" target="_blank"><?php echo htmlspecialchars($entry['ubicacion']); ?></a></td>
                        <td><?php echo $entry['cantidad'] . ' ' . $entry['unidad']; ?></td>
                        <td>
                            <?php if ($_SESSION['role'] === "Administrador" || ($_SESSION['role'] === "Técnico" && $entry['added_by'] == $_SESSION['user_id'])): ?>
                                <button class="btn btn-warning btn-sm" onclick='editRow(<?php echo json_encode($entry); ?>)'>Editar</button>
                                <a href="?vista=add_data&delete=<?php echo $entry['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center">No hay datos disponibles.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body" id="editModalBody">
                    <!-- Aquí se cargarán dinámicamente los campos de edición -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="edit_data" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addProductRow() {
    const container = document.getElementById('products-container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 product-row';
    newRow.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Producto</label>
            <select name="products[]" class="form-select">
                <option value="">Seleccione</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?php echo $prod['id']; ?>"><?php echo $prod['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Cantidad (opcional)</label>
            <input type="number" step="0.01" name="quantities[]" class="form-control" placeholder="0">
        </div>
        <div class="col-md-4">
            <label class="form-label">Unidad</label>
            <select name="units[]" class="form-select">
                <option value="kg">kg</option>
                <option value="lb">lb</option>
            </select>
        </div>
    `;
    container.appendChild(newRow);
}

function updateMunicipalitiesAdd(deptSelect) {
    const muniSelect = document.getElementById('municipality-add');
    muniSelect.innerHTML = '<option value="">Seleccione</option>';
    muniSelect.disabled = !deptSelect.value; // Enable only if a department is selected
    if (deptSelect.value) {
        fetch(`includes/get_municipalities.php?department_id=${deptSelect.value}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(muni => {
                    muniSelect.innerHTML += `<option value="${muni.id}">${muni.name}</option>`;
                });
            });
    }
}

function editRow(entry) {
    const entries = [entry]; // Convertir la fila única en un array para reutilizar la misma lógica
    populateEditModal(entries);
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

function editAll(entries) {
    populateEditModal(entries);
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

function populateEditModal(entries) {
    const modalBody = document.getElementById('editModalBody');
    let html = '';
    entries.forEach((entry, index) => {
        html += `
            <h5>Dato #${entry.id}</h5>
            <input type="hidden" name="ids[${index}]" value="${entry.id}">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="department[${index}]" class="form-select" required onchange="updateEditMunicipalities(this, ${index})">
                        <option value="">Seleccione</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" ${entry.department_id == <?php echo $dept['id']; ?> ? 'selected' : ''}>
                                <?php echo $dept['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Municipio</label>
                    <select name="municipality[${index}]" id="edit-municipality-${index}" class="form-select" required>
                        <option value="${entry.municipality_id}">${entry.municipality}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="product[${index}]" class="form-select" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" ${entry.product_id == <?php echo $prod['id']; ?> ? 'selected' : ''}>
                                <?php echo $prod['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor[${index}]" class="form-control" value="${entry.proveedor}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero[${index}]" class="form-control" value="${entry.numero}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ubicación</label>
                    <input type="text" name="ubicacion[${index}]" class="form-control" value="${entry.ubicacion}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Cantidad</label>
                    <input type="number" step="0.01" name="cantidad[${index}]" class="form-control" value="${entry.cantidad}" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unidad</label>
                    <select name="unidad[${index}]" class="form-select">
                        <option value="kg" ${entry.unidad == 'kg' ? 'selected' : ''}>kg</option>
                        <option value="lb" ${entry.unidad == 'lb' ? 'selected' : ''}>lb</option>
                    </select>
                </div>
            </div>
            <hr>
        `;
    });
    modalBody.innerHTML = html;

    // Poblar los municipios para cada entrada
    entries.forEach((entry, index) => {
        if (entry.department_id) {
            updateEditMunicipalities(document.querySelector(`select[name="department[${index}]"]`), index, entry.municipality_id);
        }
    });
}

function updateEditMunicipalities(deptSelect, index, selectedMuniId) {
    const muniSelect = document.getElementById(`edit-municipality-${index}`);
    muniSelect.innerHTML = '<option value="">Seleccione</option>';
    if (deptSelect.value) {
        fetch(`includes/get_municipalities.php?department_id=${deptSelect.value}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(muni => {
                    const option = document.createElement('option');
                    option.value = muni.id;
                    option.text = muni.name;
                    if (muni.id == selectedMuniId) {
                        option.selected = true;
                    }
                    muniSelect.appendChild(option);
                });
            });
    }
}
</script>