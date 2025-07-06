<?php
function displaySuccess() {
    echo '
    <div class="alert alert-success">
        <h4><i class="bi bi-check-circle"></i> Pet added successfully!</h4>
        <p>Your pet has been registered in the system.</p>
        <div class="mt-3">
            <a href="my_pets.php" class="btn btn-primary me-2">
                <i class="bi bi-heart"></i> View My Pets
            </a>
            <a href="add_pet.php" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Add Another Pet
            </a>
        </div>
    </div>';
}

function displayErrors($errors) {
    if (!empty($errors)) {
        echo '
        <div class="alert alert-danger">
            <h4><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h4>
            <ul>';
                foreach ($errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
            echo '
            </ul>
        </div>';
    }
}
?>