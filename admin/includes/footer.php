<?php
/**
 * StructZone Admin - Footer
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
            </div><!-- /.content-body -->
        </main>
    </div><!-- /.admin-wrapper -->
    
    <script src="assets/js/admin.js"></script>
    <?php if (isset($pageScripts)): ?>
    <?php echo $pageScripts; ?>
    <?php endif; ?>
</body>
</html>
