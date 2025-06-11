  </main> <!-- close d-flex -->
</div>
  <!-- Footer bar -->
  <footer class="text-center p-3 mt-auto w-100" footer-bar id="footerBar" style="background-color: #1b4d3e; color: #c6f2d6 ;">
    &copy; <?php echo date("Y"); ?> Hadees Bookmark App. All rights reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Dark Mode Script -->
  <script>
    // Check saved preference
    if (localStorage.getItem('darkMode') === 'enabled') {
      enableDarkMode();
    }

    function toggleDarkMode() {
      if (document.body.classList.contains('dark-mode')) {
        disableDarkMode();
      } else {
        enableDarkMode();
      }
    }

    function enableDarkMode() {
      document.body.classList.add('dark-mode');
      document.querySelectorAll('.content, .sidebar, .navbar, #footerBar').forEach(el => {
        el.classList.add('dark-mode');
      });
      localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
      document.body.classList.remove('dark-mode');
      document.querySelectorAll('.content, .sidebar, .navbar, #footer-bar').forEach(el => {
        el.classList.remove('dark-mode');
      });
      localStorage.setItem('darkMode', 'disabled');
    }
  </script>

</body>
</html>
