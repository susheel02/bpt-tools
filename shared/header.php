<?php
// Shared header for all tools
// Include schema functions if not already included
if (!class_exists('SchemaGenerator')) {
    require_once(__DIR__ . '/../includes/schema-functions.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Photography Tools' ?> - BeyondPhotoTips.com</title>
    <meta name="description" content="<?= isset($page_description) ? $page_description : 'Professional photography calculators and tools for photographers' ?>">
    <meta name="theme-color" content="#667eea">
    
    <?php if (isset($social_data)): ?>
    <!-- Social Media Meta Tags -->
    <?= SocialMediaGenerator::generateSocialMetaTags($social_data) ?>
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <?php if (isset($canonical_url)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <?php endif; ?>
    
    <!-- Resource hints for better performance -->
    <link rel="dns-prefetch" href="https://www.beyondphototips.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    
    <link rel="stylesheet" href="<?= isset($base_url) ? $base_url : '' ?>assets/css/tools.css?v=0.0.3">
    <?php if (isset($css_path)): ?>
    <link rel="stylesheet" href="<?= $css_path ?>">
    <?php endif; ?>
    <?php if (isset($manifest_path)): ?>
    <link rel="manifest" href="<?= $manifest_path ?>">
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZZXPCE599N"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-ZZXPCE599N');
    </script>
    
    <?php if (isset($schema_data)): ?>
    <!-- JSON-LD Schema Markup -->
    <?= SchemaGenerator::generateCalculatorSchema($schema_data) ?>
    <?php endif; ?>
    
    <?php if (isset($breadcrumb_data)): ?>
    <!-- Breadcrumb Schema -->
    <?= SchemaGenerator::generateBreadcrumbSchema($breadcrumb_data) ?>
    <?php endif; ?>
</head>
<body>
    <header id="header" class="tools-header">
        <div class="tools-container">
            <div class="header-content">
                <div class="site-branding">
                    <a href="https://www.beyondphototips.com/" class="site-logo-container" rel="home">
                        <div class="logo-content">
                            <img src="https://www.beyondphototips.com/wp-content/uploads/2020/03/BPT-Intermediate-Logo.jpg.webp" 
                                 class="default-logo" alt="Beyond Photo Tips Logo" width="300" height="78" 
                                 loading="lazy" decoding="async">
                            <p class="site-description">Photography for the Serious Amateur</p>
                        </div>
                    </a>
                </div>
                <nav class="header-menu">
                    <ul class="menu">
                        <li><a href="https://www.beyondphototips.com/">Home</a></li>
                        <li><a href="https://www.beyondphototips.com/get-better-at-photography/">Start Here</a></li>
                        <li><a href="https://www.beyondphototips.com/category/equipment/">Equipment</a></li>
                        <li><a href="https://www.beyondphototips.com/category/basics/">Basics</a></li>
                        <li><a href="https://www.beyondphototips.com/category/software/">Software</a></li>
                        <li class="current"><a href="<?= $base_url ?? '/' ?>">Tools</a></li>
                        <li><a href="https://www.beyondphototips.com/about-beyond-phototips/">About Us</a></li>
                    </ul>
                </nav>
                
                <?php if (isset($sharing_data)): ?>
                <!-- Social Sharing Buttons -->
                <?= SocialMediaGenerator::generateSharingButtons($sharing_data) ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($show_back_button) && $show_back_button): ?>
        <div class="tools-container">
            <div class="back-button-section">
                <a href="<?= $base_url ?? '../' ?>" class="back-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Tools
                </a>
            </div>
        </div>
        <?php endif; ?>
    </header>