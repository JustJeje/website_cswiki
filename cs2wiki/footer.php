<?php
$footer_year = date("Y");
?>
<footer class="cs2-footer mt-auto py-4">
    <div class="container text-center">
        <div class="mb-2">
            <img src="images/logo.png" alt="CS2 Knife Wiki" class="footer-logo-img"
                 onerror="this.style.display='none'">
        </div>
        <p class="footer-brand"><span class="accent">CS2 Knife Wiki</span></p>
        <p class="footer-sub">Not affiliated with Valve Corporation. Fan-made educational project.</p>
        <p class="footer-copy">© <?= $footer_year ?> CS2 Knife Wiki. Built with PHP & Bootstrap.</p>
    </div>
</footer>
