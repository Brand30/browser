<?php
function renderSearchFilters($departments, $products) {
    global $pdo;
    $filter_dept = isset($_GET['department']) ? $_GET['department'] : '';
    $filter_muni = isset($_GET['municipality']) ? $_GET['municipality'] : '';
    $filter_prod = isset($_GET['product']) ? $_GET['product'] : '';

    $query = "SELECT d.name as department, m.name as municipality, p.name as product, 
              ud.proveedor, ud.numero, ud.ubicacion, ud.cantidad, ud.unidad 
              FROM user_data ud 
              JOIN departments d ON ud.department_id = d.id 
              JOIN municipalities m ON ud.municipality_id = m.id 
              JOIN products p ON ud.product_id = p.id 
              WHERE 1=1";
    $params = [];
    if ($filter_dept) {
        $query .= " AND ud.department_id = ?";
        $params[] = $filter_dept;
    }
    if ($filter_muni) {
        $query .= " AND ud.municipality_id = ?";
        $params[] = $filter_muni;
    }
    if ($filter_prod) {
        $query .= " AND ud.product_id = ?";
        $params[] = $filter_prod;
    }
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    global $filtered_data;
    $filtered_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all municipalities if no department is selected
    $municipalities = $filter_dept ? getMunicipalities($filter_dept) : getMunicipalities();
    ?>
    <form method="GET">
        <input type="hidden" name="vista" value="buscador">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Departamento</label>
                <select name="department" id="department" class="form-select" onchange="updateMunicipalities(this)">
                    <option value="">Todos</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo $filter_dept == $dept['id'] ? 'selected' : ''; ?>>
                            <?php echo $dept['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Municipio</label>
                <select name="municipality" id="municipality" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($municipalities as $muni): ?>
                        <option value="<?php echo $muni['id']; ?>" <?php echo $filter_muni == $muni['id'] ? 'selected' : ''; ?>>
                            <?php echo $muni['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <select name="product" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($products as $prod): ?>
                        <option value="<?php echo $prod['id']; ?>" <?php echo $filter_prod == $prod['id'] ? 'selected' : ''; ?>>
                            <?php echo $prod['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="filter" class="btn btn-primary">Filtrar</button>
    </form>
    <?php
}
?>