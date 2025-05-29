export function validatorProductForm(form) {
    let isValid = true;

    form.clearErrors();

    // Validación nombre
    if (!form.name.trim()) {
        form.setError('name', 'El campo nombre es obligatorio.');
        isValid = false;
    } else if (form.name.trim().length < 3) {
        form.setError('name', 'El campo nombre debe tener al menos 3 caracteres.');
        isValid = false;
    } else if (form.name.trim().length > 100) {
        form.setError('name', 'El campo nombre debe tener como máximo 100 caracteres.');
        isValid = false;
    } else if (!/^[\p{L}\s]+$/u.test(form.name)) {
        form.setError('name', 'El campo nombre solo puede contener letras y espacios.');
        isValid = false;
    }

    // validacion ubicacion
    if (!form.location_id) {
        form.setError('location_id', 'El campo ubicación es obligatorio.');
        isValid = false;
    }

    return isValid;
}
