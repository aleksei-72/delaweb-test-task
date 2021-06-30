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
        document.getElementById('error-password-dont-match').classList.add('d-none');
    }

    if (!isValidate) {
        return;
    }

    //Send data to server
    alert('ok');
}
