const status = ['disponible', 'ocupada', 'reservada', 'en limpieza'];

export function validatorTableForm(form) {
    let isValid = true;

    form.clearErrors();

    if (!form.name.trim()) {
        form.setError('name', 'El nombre es obligatorio.');
        isValid = false;
    } else if (form.name.trim().length < 3) {
        form.setError('name', 'El nombre debe tener al menos 3 caracteres.');
        isValid = false;
    } else if (form.name.trim().length > 50) {
        form.setError('name', 'El nombre debe tener como máximo 50 caracteres.');
        isValid = false;
    } else if (!/^[a-zA-Z0-9\s]+$/.test(form.name)) {
        form.setError('name', 'El nombre solo puede contener letras y numeros.');
        isValid = false;
    }

    if (!form.status) {
        form.setError('status', 'El status es obligatorio.');
        isValid = false;
    } else if (!status.includes(form.status)) {
        form.setError('status', 'El status es incorrecto.');
        isValid = false;
    }

    return isValid;
}
