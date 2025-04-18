    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Information</h3>
                <p><i class="fas fa-map-marker-alt"></i> JNTUA College of Engineering</p>
                <p>Anantapur - 515002</p>
                <p>Andhra Pradesh, India</p>
                <p><i class="fas fa-phone"></i>08554-273013</p>
                <p><i class="fas fa-envelope"></i> principal.cea@jntua.ac.in</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="<?php echo $base_url; ?>dashboard.php" style="color:white;">Dashboard</a></p>
                <p><a href="<?php echo $base_url; ?>students/view.php" style="color:white;">View Students</a></p>
                <p><a href="<?php echo $base_url; ?>students/add.php" style="color:white;">Add Student</a></p>
                <p><a href="<?php echo $base_url; ?>fees/add.php" style="color:white;">Add Fee</a></p>
            </div>
            <div class="footer-section">
                <h3>About JNTUACEA</h3>
                <p>JNTUA College of Engineering, Anantapur was established in 1946 and is an autonomous constituent college of Jawaharlal Nehru Technological University Anantapur.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> JNTUACEA Fee Management System. All rights reserved.</p>
        </div>
    </footer>

    <?php if (isset($additional_scripts)) echo $additional_scripts; ?>
</body>
</html> 