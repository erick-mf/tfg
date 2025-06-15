const typeImages = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

export function validatorMenuItemForm(form) {
    let isValid = true;

    form.clearErrors();

    if (!form.name.trim()) {
        form.setError('name', 'El nombre es obligatorio.');
        isValid = false;
    } else if (form.name.trim().length < 3) {
        form.setError('name', 'El nombre debe tener al menos 3 caracteres.');
        isValid = false;
    } else if (form.name.trim().length > 100) {
        form.setError('name', 'El nombre debe tener como máximo 100 caracteres.');
        isValid = false;
    } else if (!/^[\p{L}\s]+$/u.test(form.name)) {
        form.setError('name', 'El nombre solo puede contener letras y espacios.');
        isValid = false;
    }

    if (!form.price) {
        form.setError('price', 'El precio es obligatorio.');
        isValid = false;
    } else if (form.price < 0) {
        form.setError('price', 'El precio debe ser mayor o igual a 0.');
        isValid = false;
    } else if (!/^[0-9]+(\.[0-9]+)?$/.test(form.price)) {
        form.setError('price', 'El precio debe ser un número.');
        isValid = false;
    }

    if (form.image_path) {
        if (form.image_path.size > 2000000) {
            form.setError('image_path', 'La imagen debe tener como máximo 2MB.');
            isValid = false;
        } else if (!form.image_path.type.includes('image')) {
            form.setError('image_path', 'El archivo debe ser una imagen.');
            isValid = false;
        } else if (!typeImages.includes(form.image_path.type)) {
            form.setError('image_path', 'El archivo debe ser una imagen con extensiones jpeg, png, jpg o webp.');
            isValid = false;
        }
    }

    if (!form.location) {
        form.setError('location', 'La ubicación es obligatoria.');
        isValid = false;
    }

    if (!form.menu_category_id) {
        form.setError('menu_category_id', 'La categoría es obligatoria.');
        isValid = false;
    }
    return isValid;
}
