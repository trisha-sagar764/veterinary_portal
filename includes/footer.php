<footer style="background-color: #0074c1; color: white; padding: 20px 0;">
  <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; text-align: center;">
    
    <div style="max-width: 300px;">
      <h4>About Department</h4>
      <p>The Department of Animal Husbandry & Veterinary Services provides comprehensive animal healthcare services across Andaman & Nicobar Islands.</p>
    </div>

    <div style="max-width: 300px;">
      <h4>Contact Us</h4>
      <p>Department of Animal Husbandry and Veterinary Services<br>
      Haddo, Port Blair Andaman and Nicobar Islands<br>
      ☎ 03192-233286(O)<br>
      ✉ dir-ah[at]and[dot]nic[dot]in</p>
    </div>

    <div style="text-align: center;">
      <h4>Designed & Developed by:</h4>
      <img src="assets/images/iconic_logo_v2.png" alt="NIC Logo" style="height: 60px;">
    </div>

  </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Main JS with base URL -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<?php if (basename($_SERVER['PHP_SELF']) == 'registration.php'): ?>
<!-- Registration JS with cache busting -->
<script src="<?= BASE_URL ?>assets/js/registration.js?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].BASE_URL.'assets/js/registration.js') ?>"></script>

<!-- Initialize base path for AJAX calls -->
<script>
     
    console.log('Base path set to:', BASE_PATH);
    console.log('Registration JS loaded:', 
        '<?= BASE_URL ?>assets/js/registration.js?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].BASE_URL.'assets/js/registration.js') ?>');
</script>
<?php endif; ?>
</body>
</html>