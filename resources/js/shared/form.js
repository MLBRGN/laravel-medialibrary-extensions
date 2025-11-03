
/** Helpers (private to this module) */
export function setFormElementsDisabled(forms, disabled) {
    forms.forEach(form => {
        form.querySelectorAll('input:not([type="hidden"]), button')
            .forEach(el => el.disabled = disabled);
    });
}

export function disableFormElements(forms) {
    setFormElementsDisabled(forms, true);
}

export function enableFormElements(forms) {
    setFormElementsDisabled(forms, false);
}

export function getFormData(formElement) {
    const formData = new FormData();
    formElement.querySelectorAll('input').forEach(input => {
        if (input.type === 'file') {
            Array.from(input.files).forEach(file => {
                formData.append(input.name, file);
            });
        } else {
            formData.append(input.name, input.value);
        }
    });

    // formData.forEach((value, key) => {
    //     console.log(key, value);
    // });
    return formData;
}
