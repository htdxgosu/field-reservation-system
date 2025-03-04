document.addEventListener('DOMContentLoaded', function () {
    const editUserForm = document.getElementById('editUserForm');
    editUserForm.addEventListener('submit', function (event) {
        event.preventDefault();

        let phone = document.getElementById('phone').value;
        let email = document.getElementById('email').value;
        let valid = true;
        let errorMessage = '';

        // Kiểm tra số điện thoại
        const phonePattern = /^0[0-9]{9}$/;
        if (!phonePattern.test(phone)) {
            valid = false;
            errorMessage += 'Số điện thoại không hợp lệ.\n';
        }

        // Kiểm tra email
        const emailPattern = /^[a-zA-Z0-9._%+-]{3,}@gmail\.com$/;
        if (!emailPattern.test(email)) {
            valid = false;
            errorMessage += 'Email không hợp lệ.\n';
        }

        if (valid) {
            const formData = new FormData(this);
            fetch('/edit-user', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.type === 'error') {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                            showConfirmButton: true,
                        });
                    } else if (data.type === 'success') {
                        Swal.fire({
                            icon: 'success',
                            text: data.message,
                            showConfirmButton: true,
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
        } else {
            Swal.fire({
                icon: 'error',
                text: errorMessage,
                showConfirmButton: true,
            });
        }
    });
});