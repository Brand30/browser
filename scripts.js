function updateMunicipalities(deptSelect) {
    const muniSelect = document.getElementById('municipality');
    muniSelect.innerHTML = '<option value="">Todos</option>';
    
    // If no department is selected, fetch all municipalities
    const url = deptSelect.value ? `includes/get_municipalities.php?department_id=${deptSelect.value}` : 'includes/get_municipalities.php';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            data.forEach(muni => {
                muniSelect.innerHTML += `<option value="${muni.id}">${muni.name}</option>`;
            });
        });
}