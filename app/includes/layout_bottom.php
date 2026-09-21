<?php
declare(strict_types=1);
// Include at the very end of a page. Set $footerHtml before including for
// page-specific footer text; otherwise a generic line is shown.
$footerHtml = $footerHtml ?? '<p>Only people you\'ve added as a user can see this page. Nothing here is exposed publicly.</p>';
?>
<footer>
  <?= $footerHtml /* pre-built by caller, esc() already applied where needed */ ?>
</footer>

</div>
</body>
</html>
