function openEditCredModal(userId, username, currentCred) {
    document.getElementById("edit_user_id").value = userId;
    document.getElementById("edit_username").textContent = username;
    document.getElementById("new_credential_level").value = currentCred;
    var modal = new bootstrap.Modal(document.getElementById("editCredModal"));
    modal.show();
}

function openResetPwModal(userId, username) {
    document.getElementById("reset_user_id").value = userId;
    document.getElementById("reset_username").textContent = username;
    document.getElementById("new_password").value = "";
    var modal = new bootstrap.Modal(document.getElementById("resetPwModal"));
    modal.show();
}