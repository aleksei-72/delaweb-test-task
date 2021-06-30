let validateElementsIdList = ['input-first-name', 'input-last-name', 'input-phone',
    'input-organization', 'input-password1', 'input-password2'];

window.onload = function () {

    validateElementsIdList.forEach(function (id) {

        let element = document.getElementById(id);

        element.onchange = function () {

            if(this.value === '') {
                this.classList.remove('is-invalid');
                this.classList.remove('is-valid');
                return;
            }

            if(this.checkValidity()) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                return;
            }

            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        }

        element.onchange();
    });
}

document.getElementById('submit-btn').onclick = function (e) {
    e.preventDefault();

    let isValidate = true;

    validateElementsIdList.forEach(function (id) {

        let input = document.getElementById(id);

        if (!input.checkValidity() || input.value.length === 0)
        {
            input.classList.add('is-invalid');
            isValidate = false;
        }
    });

    if (document.getElementById('input-password1').value !== document.getElementById('input-password2').value){

        document.getElementById('input-password1').classList.add('is-invalid');
        document.getElementById('input-password2').classList.add('is-invalid');
        document.getElementById('error-password-dont-match').classList.remove('d-none');

        isValidate = false;
    } else {
        document.getElementById('input-password1').classList.remove('is-invalid');
        document.getElementById('input-password2').classList.remove('is-invalid');
        document.getElementById('label-error-password-dont-match').classList.add('d-none');
    }

    if (!isValidate) {
        return;
    }


    //Send data to server
    axios.post('/registration', {

        'first_name': document.getElementById('input-first-name').value,
        'last_name': document.getElementById('input-last-name').value,
        'phone': document.getElementById('input-phone').value,
        'organization': document.getElementById('input-organization').value,
        'password': document.getElementById('input-password1').value,
        'invitatory_id': document.getElementById('input-invitatory').value

    }).then(function (response) {
        clearErrorLabels();

        window.location = '/login';

    }).catch(function (error) {

        clearErrorLabels();

        let errorType = error.response.data.error;

        switch (errorType) {

            case E_NOT_UNIQUE_PHONE:
                document.getElementById('label-error-phone-dont-unique').classList.remove('d-none');
                break;
            case E_INVALID_INVITATORY_ID:
                document.getElementById('label-error-invitatory-not-found').classList.remove('d-none');
                break;
            default :
                document.getElementById('label-undefined-error').classList.remove('d-none');
                break;
        }

        console.log(error);
    })
}

function clearErrorLabels() {
    document.getElementById('label-error-phone-dont-unique').classList.add('d-none');
    document.getElementById('label-error-invitatory-not-found').classList.add('d-none');
    document.getElementById('label-undefined-error').classList.add('d-none');
}
