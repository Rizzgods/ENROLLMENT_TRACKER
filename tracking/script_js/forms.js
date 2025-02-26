document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');

    form.addEventListener('submit', function(event) {
        const requiredFields = ['FNAME', 'LNAME', 'SEX', 'BDAY', 'BPLACE', 'STATUS', 'NATIONALITY', 'RELIGION', 'CONTACT_NO', 'HOME_ADD', 'SEMESTER', 'SYEAR', 'EMAIL', 'GUARDIAN', 'GUARDIAN_ADDRESS', 'GCONTACT'];
        let valid = true;

        requiredFields.forEach(function(field) {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        if (!valid) {
            event.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});