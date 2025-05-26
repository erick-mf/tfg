export function validatorLoginForm(form, errors) {
    let isValid = true;

    errors.value = {
        password: '',
    };

    // Validación contraseña
    if (!form.password.trim()) {
        errors.value.password = 'El contraseña es obligatorio.';
        isValid = false;
    } else if (form.password.trim().length !== 6) {
        errors.value.password = 'La contraseña es incorrecta.';
        isValid = false;
    }

    return isValid;
}
