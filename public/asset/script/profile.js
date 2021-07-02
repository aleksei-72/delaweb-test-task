let validateElementsIdList = ['input-first-name', 'input-last-name', 'input-phone',
    'input-organization'];

document.getElementById('logout-btn').onclick = logout;

function logout(e) {
    if (e) {
        e.preventDefault();
    }

    deleteCookie('token');
    window.location.reload();
}

window.onload = function () {

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
    axios.patch('/profile', {

        'first_name': document.getElementById('input-first-name').value,
        'last_name': document.getElementById('input-last-name').value,
        'phone': document.getElementById('input-phone').value,
        'organization': document.getElementById('input-organization').value,
        'invitatory_id': document.getElementById('input-invitatory').value

    }).then(function (response) {
        clearErrorLabels();

        swal({
            title: "Обновление данных",
            text: "Данные успешно обновлены!",
            icon: "success",
        });

    }).catch(function (error) {

        clearErrorLabels();

        let errorType = error.response.data.error;

        switch (errorType) {

            case E_UNAUTHORIZE:
                logout(null);
                break;

            case E_NOT_UNIQUE_PHONE:
                document.getElementById('label-error-phone-dont-unique').classList.remove('d-none');
                document.getElementById('input-phone').classList.add('is-invalid');
                break;

            case E_INVALID_INVITATORY_ID:
                document.getElementById('label-error-invitatory-not-found').classList.remove('d-none');
                break;

            default :
                swal({
                    title: "Обработка данных",
                    text: "Произошла непредвиденная ошибка",
                    icon: "error",
                });
                break;
        }

        console.log(error);
    })
}

function clearErrorLabels() {
    document.getElementById('label-error-phone-dont-unique').classList.add('d-none');
    document.getElementById('label-error-invitatory-not-found').classList.add('d-none');
}
