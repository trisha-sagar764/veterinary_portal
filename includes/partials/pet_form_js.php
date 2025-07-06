<?php
// includes/partials/pet_form_js.php
?>
<script>
// AJAX for breed loading based on species selection
document.getElementById('species_id').addEventListener('change', function() {
    const speciesId = this.value;
    const breedSelect = document.getElementById('breed_id');
    
    if (speciesId) {
        fetch('includes/get_breeds.php?species_id=' + speciesId)
            .then(response => response.json())
            .then(data => {
                breedSelect.innerHTML = '<option value="">Select Breed</option>';
                data.forEach(breed => {
                    const option = document.createElement('option');
                    option.value = breed.breed_id;
                    option.textContent = breed.breed_name;
                    breedSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    } else {
        breedSelect.innerHTML = '<option value="">Select Breed</option>';
    }
});

// Profile picture preview
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profile-picture-preview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Form validation
(function() {
    'use strict';
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>