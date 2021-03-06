let validateElementsIdList = ['input-phone', 'input-password'];

window.onload = function () {

    if (getCookie('token')) {
        window.location = '/profile';
    }

    validateElementsIdList.forEach(function (id) {

        let element = document.getElementById(id);

        element.onchange = function () {

            if(this.value === '') {
                this.classList.remove('is-invalid');
                return;
            }

            if(this.checkValidity()) {
                this.classList.remove('is-invalid');
                return;
            }

            this.classList.add('is-invalid');
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


    if (!isValidate) {
        return;
    }


    //Send data to server
    axios.post('/login', {
        'phone': document.getElementById('input-phone').value,
        'password': document.getElementById('input-password').value,

    }).then(function (response) {
        clearErrorLabels();

        window.location = '/profile';

    }).catch(function (error) {

        clearErrorLabels();

        let errorType = error.response.data.error;

        switch (errorType) {

            case E_INVALID_PASSWORD:
                document.getElementById('label-error-invalid-password').classList.remove('d-none');
                document.getElementById('input-password').classList.add('is-invalid');
                break;

            case E_USER_NOT_FOUND:
                document.getElementById('label-error-phone-not-found').classList.remove('d-none');
                document.getElementById('input-phone').classList.add('is-invalid');
                break;

            default:
                swal({
                    title: "?????????????????? ????????????",
                    text: "?????????????????? ???????????????????????????? ????????????",
                    icon: "error",
                });
                break;
        }

        console.log(error);
    })
}

function clearErrorLabels() {
    document.getElementById('label-error-phone-not-found').classList.add('d-none');
    document.getElementById('label-error-invalid-password').classList.add('d-none');
}
