<?php

$backtrace = debug_backtrace();
$callerFile = isset($backtrace[0]['file']) ? $backtrace[0]['file'] : 'unknown';
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPageParam = isset($_GET['per_page']) ? '&per_page=' . (int)$_GET['per_page'] : '';
$paginationQueryExtra = isset($paginationQueryExtra) ? $paginationQueryExtra : '';

?>

<?php if (isset($total_pages) && $total_pages > 1) { ?>
<div class="d-flex justify-content-end pagination-div">
    <?php
        if ($currentPage == 1) {
            // ništa za <<
        } else { ?>
            <a href='<?php echo basename($callerFile)."?page=1" . $perPageParam . $paginationQueryExtra; ?>' class="links"> << </a>
        <?php }
        if ($currentPage >= 3) { ?>
        <span>...</span>
        <?php }
        for ($page = 1; $page <= $total_pages; $page++):
            if (abs($page - $currentPage) >= 2) {
                // preskoči
            } else {
                if ($currentPage == $page) { ?>
                    <p class="activePage"><?php echo $page; ?></p>
                <?php } else { ?>
                    <a href='<?php echo basename($callerFile)."?page=$page" . $perPageParam . $paginationQueryExtra; ?>' class="links">
                        <?php echo $page; ?>
                    </a>
                <?php }
            }
        endfor;
        if ($currentPage <= ($total_pages - 2)) { ?>
            <span>...</span>
        <?php }
        if ($currentPage == $total_pages) {
            // ništa za >>
        } else { ?>
            <a href='<?php echo basename($callerFile)."?page=$total_pages" . $perPageParam . $paginationQueryExtra; ?>' class="links"> >> </a>
        <?php }
    ?>
</div>
<?php } ?>