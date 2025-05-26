export function validatorUserForm(form, errors, user = null) {
    let isValid = true;

    errors.value = {
        name: '',
        surnames: '',
        password: '',
        phone: '',
        phone_emergency: '',
        role: '',
    };

    // Validación nombre
    if (!form.name.trim()) {
        errors.value.name = 'El campo nombre es obligatorio.';
        isValid = false;
    } else if (form.name.trim().length < 3) {
        errors.value.name = 'El campo nombre debe tener al menos 3 caracteres.';
        isValid = false;
    } else if (form.name.trim().length > 100) {
        errors.value.name = 'El campo nombre debe tener como máximo 100 caracteres.';
        isValid = false;
    } else if (!/^[a-zA-ZaáéíóúAÁÉÍÓÚñÑ\s]+$/.test(form.name)) {
        errors.value.name = 'El campo nombre solo puede contener letras y espacios.';
        isValid = false;
    }

    // Validación apellidos
    if (!form.surnames.trim()) {
        errors.value.surnames = 'El campo apellidos es obligatorio.';
        isValid = false;
    } else if (form.surnames.trim().length < 3) {
        errors.value.surnames = 'El campo apellidos debe tener al menos 3 caracteres.';
        isValid = false;
    } else if (form.surnames.trim().length > 150) {
        errors.value.surnames = 'El campo apellidos debe tener como máximo 150 caracteres.';
        isValid = false;
    } else if (!/^[a-zA-ZaáéíóúAÁÉÍÓÚñÑ\s]+$/.test(form.surnames)) {
        errors.value.surnames = 'El campo apellidos solo puede contener letras y espacios.';
        isValid = false;
    }

    // Validación teléfono
    if (!form.phone) {
        errors.value.phone = 'El campo teléfono es obligatorio.';
        isValid = false;
    } else if (form.phone.length !== 9) {
        errors.value.phone = 'El campo teléfono debe tener 9 dígitos.';
        isValid = false;
    } else if (!/^[0-9]+$/.test(form.phone)) {
        errors.value.phone = 'El campo teléfono solo puede contener números.';
        isValid = false;
    }

    // Validación teléfono emergencia (opcional)
    if (form.phone_emergency && form.phone_emergency.length !== 9) {
        errors.value.phone_emergency = 'El campo teléfono de emergencia debe tener 9 dígitos.';
        isValid = false;
    } else if (form.phone_emergency && !/^[0-9]+$/.test(form.phone_emergency)) {
        errors.value.phone_emergency = 'El campo teléfono de emergencia solo puede contener números.';
        isValid = false;
    }

    // Validación rol
    if (!form.role) {
        errors.value.role = 'El campo rol es obligatorio.';
        isValid = false;
    } else if (form.role === 'admin') {
        errors.value.role = 'El rol seleccionado no es válido.';
        isValid = false;
    }

    // Validación password
    if (!form.id || !user) {
        if (!form.password.trim()) {
            errors.value.password = 'El campo password es obligatorio.';
            isValid = false;
        } else if (form.password.length !== 6) {
            errors.value.password = 'El campo password debe tener 6 dígitos.';
            isValid = false;
        }
    }

    return isValid;
}
