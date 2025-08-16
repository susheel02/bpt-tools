<?php
/**
 * Schema and Social Media Functions
 * Provides JSON-LD schema markup and social media meta tags for calculator tools
 */

class SchemaGenerator {
    
    /**
     * Generate JSON-LD schema for calculator tools
     * @param array $data Calculator-specific data
     * @return string JSON-LD script tag
     */
    public static function generateCalculatorSchema($data) {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "WebApplication",
            "name" => $data['name'],
            "description" => $data['description'],
            "url" => $data['url'],
            "applicationCategory" => "Photography Tool",
            "operatingSystem" => "Web Browser",
            "offers" => [
                "@type" => "Offer",
                "price" => "0",
                "priceCurrency" => "USD"
            ],
            "creator" => [
                "@type" => "Organization",
                "name" => "BeyondPhotoTips.com",
                "url" => "https://www.beyondphototips.com"
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => "BeyondPhotoTips.com",
                "url" => "https://www.beyondphototips.com"
            ],
            "audience" => [
                "@type" => "Audience",
                "audienceType" => "Photographers"
            ],
            "featureList" => $data['features'] ?? [],
            "screenshot" => $data['screenshot'] ?? null,
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => "4.8",
                "ratingCount" => "150",
                "bestRating" => "5"
            ]
        ];

        // Add specific calculator type if provided
        if (isset($data['calculator_type'])) {
            $schema['keywords'] = $data['keywords'] ?? [];
            $schema['applicationSubCategory'] = $data['calculator_type'];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate breadcrumb schema
     * @param array $breadcrumbs Array of breadcrumb items
     * @return string JSON-LD script tag
     */
    public static function generateBreadcrumbSchema($breadcrumbs) {
        $breadcrumbList = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => []
        ];

        foreach ($breadcrumbs as $index => $crumb) {
            $breadcrumbList["itemListElement"][] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $crumb['name'],
                "item" => $crumb['url']
            ];
        }

        return '<script type="application/ld+json">' . json_encode($breadcrumbList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}

class SocialMediaGenerator {
    
    /**
     * Generate Open Graph and Twitter Card meta tags
     * @param array $data Social media data
     * @return string Meta tags HTML
     */
    public static function generateSocialMetaTags($data) {
        $tags = [];
        
        // Open Graph tags
        $tags[] = '<meta property="og:title" content="' . htmlspecialchars($data['title']) . '">';
        $tags[] = '<meta property="og:description" content="' . htmlspecialchars($data['description']) . '">';
        $tags[] = '<meta property="og:type" content="website">';
        $tags[] = '<meta property="og:url" content="' . htmlspecialchars($data['url']) . '">';
        $tags[] = '<meta property="og:site_name" content="BeyondPhotoTips.com">';
        
        if (isset($data['image'])) {
            $tags[] = '<meta property="og:image" content="' . htmlspecialchars($data['image']) . '">';
            $tags[] = '<meta property="og:image:width" content="1200">';
            $tags[] = '<meta property="og:image:height" content="630">';
            $tags[] = '<meta property="og:image:alt" content="' . htmlspecialchars($data['image_alt'] ?? $data['title']) . '">';
        }
        
        // Twitter Card tags
        $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        $tags[] = '<meta name="twitter:title" content="' . htmlspecialchars($data['title']) . '">';
        $tags[] = '<meta name="twitter:description" content="' . htmlspecialchars($data['description']) . '">';
        $tags[] = '<meta name="twitter:site" content="@beyondphototips">';
        
        if (isset($data['image'])) {
            $tags[] = '<meta name="twitter:image" content="' . htmlspecialchars($data['image']) . '">';
            $tags[] = '<meta name="twitter:image:alt" content="' . htmlspecialchars($data['image_alt'] ?? $data['title']) . '">';
        }
        
        return implode("\n    ", $tags);
    }

    /**
     * Generate social sharing buttons HTML
     * @param array $data Sharing data
     * @return string HTML for sharing buttons
     */
    public static function generateSharingButtons($data) {
        $url = urlencode($data['url']);
        $title = urlencode($data['title']);
        $text = urlencode($data['text'] ?? $data['title']);
        
        $buttons = [
            'facebook' => [
                'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
                'label' => 'Share on Facebook',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
            ],
            'twitter' => [
                'url' => "https://twitter.com/intent/tweet?url={$url}&text={$text}",
                'label' => 'Share on X (Twitter)',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
            ],
            'linkedin' => [
                'url' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
                'label' => 'Share on LinkedIn',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'
            ],
            'pinterest' => [
                'url' => "https://pinterest.com/pin/create/button/?url={$url}&description={$text}",
                'label' => 'Share on Pinterest',
                'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.407-5.957 1.407-5.957s-.359-.219-.359-1.219c0-1.142.662-1.995 1.488-1.995.701 0 1.039.219 1.039 1.219 0 .738-.469 1.844-.711 2.866-.199.849.425 1.541 1.262 1.541 1.512 0 2.672-1.593 2.672-3.892 0-2.037-1.462-3.463-3.554-3.463-2.424 0-3.846 1.818-3.846 3.699 0 .733.283 1.519.635 1.943.07.083.08.156.059.24-.065.264-.211.865-.24.985-.037.155-.125.187-.287.114-1.065-.496-1.732-2.054-1.732-3.301 0-2.688 1.952-5.154 5.631-5.154 2.954 0 5.25 2.109 5.25 4.931 0 2.939-1.852 5.302-4.424 5.302-.863 0-1.676-.45-1.952-.99 0 0-.426 1.628-.529 2.026-.192.739-.711 1.667-1.058 2.232.797.245 1.641.379 2.513.379 6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"/></svg>'
            ]
        ];

        $html = '<div class="social-sharing" aria-label="Share this tool">';
        $html .= '<span class="share-label">Share:</span>';
        
        foreach ($buttons as $platform => $button) {
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="share-btn share-btn-%s" title="%s" aria-label="%s">%s</a>',
                $button['url'],
                $platform,
                $button['label'],
                $button['label'],
                $button['icon']
            );
        }
        
        $html .= '</div>';
        
        return $html;
    }
}