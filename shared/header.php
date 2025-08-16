<?php
// Shared header for all tools
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Photography Tools' ?> - BeyondPhotoTips.com</title>
    <meta name="description" content="<?= isset($page_description) ? $page_description : 'Professional photography calculators and tools for photographers' ?>">
    <meta name="theme-color" content="#667eea">
    <link rel="stylesheet" href="<?= isset($css_path) ? $css_path : 'assets/css/tools.css' ?>">
    
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
</head>
<body>
    <header id="header" class="tools-header">
        <div class="tools-container">
            <div class="header-content">
                <div class="site-branding">
                    <a href="https://www.beyondphototips.com/" class="site-logo-container" rel="home">
                        <img src="https://www.beyondphototips.com/wp-content/uploads/2020/03/BPT-Intermediate-Logo.jpg" 
                             class="default-logo" alt="Beyond Photo Tips Logo" width="300" height="78">
                    </a>
                    <p class="site-description">Photography for the Serious Amateur</p>
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
            </div>
        </div>
    </header>