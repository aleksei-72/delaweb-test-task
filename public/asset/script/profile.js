document.getElementById('logout-btn').onclick = function () {
    deleteCookie('token');
    window.location.reload();
}