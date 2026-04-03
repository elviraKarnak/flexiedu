<?php
get_header('dashboard'); 

// Paths from plugin
$base_path   = defined('FlexiEdu_Courses_PATH') 
    ? FlexiEdu_Courses_PATH . 'templates/organization/pages/' 
    : '';

$layout_path = defined('FlexiEdu_Courses_PATH') 
    ? FlexiEdu_Courses_PATH . 'templates/organization/layout/' 
    : '';

// Safety check
if (!$base_path || !$layout_path) {
    echo '<p>Plugin not active</p>';
    get_footer();
    return;
}

// Page detect
$page = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

$file = $base_path . $page . '.php';

if (!file_exists($file)) {
    $file = $base_path . 'dashboard.php';
}
?>

<div class="dashboard-content-wrapper">

    <div class="learner-dashboard-left">
        <?php include $layout_path . 'admin-l-sidebar.php'; ?>
    </div>

    <div class="learner-dashboard-right">

        <div class="top_section">
            <?php include $layout_path . 'org-header.php'; ?>
        </div>

        <div class="learner-middle-container">
            <div class="learner-main">

                <div class="dashboard-main-content">

                    <div class="banner_section">
                        <?php include $layout_path . 'org-banner.php'; ?>
                    </div>

                    <!-- MAIN CONTENT -->
                    <?php the_content(); ?>

                </div>

                <div class="learner-sidebar-right">
                    <?php include $layout_path . 'org-r-sidebar.php'; ?>
                </div>

            </div>
        </div>

        <div class="bottom_section">
            <?php include $layout_path . 'org-footer.php'; ?>
        </div>

    </div>

</div>

<?php
get_footer('dashboard'); 