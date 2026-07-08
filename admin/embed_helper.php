<?php
/**
 * Helper functions for embedding external content
 * Save this file as: embed_helper.php in your ROYALCMS root directory
 */

/**
 * Detect the type of URL and get embed code
 */
function detectEmbedType($url) {
    $url = strtolower($url);
    
    // Facebook detection
    if (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.com') !== false) {
        return 'facebook';
    }
    
    // Instagram detection
    if (strpos($url, 'instagram.com') !== false || strpos($url, 'instagr.am') !== false) {
        return 'instagram';
    }
    
    // YouTube detection
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        return 'youtube';
    }
    
    // Twitter/X detection
    if (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false) {
        return 'twitter';
    }
    
    // TikTok detection
    if (strpos($url, 'tiktok.com') !== false) {
        return 'tiktok';
    }
    
    // Vimeo detection
    if (strpos($url, 'vimeo.com') !== false) {
        return 'vimeo';
    }
    
    return null;
}

/**
 * Extract video ID from YouTube URL
 */
function getYouTubeId($url) {
    $patterns = [
        '/(?:youtube\\.com\\/watch\\?v=|youtu\\.be\\/)([^&\\?#]+)/',
        '/youtube\\.com\\/embed\\/([^&\\?#]+)/',
        '/youtube\\.com\\/v\\/([^&\\?#]+)/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * Extract Instagram post ID
 */
function getInstagramId($url) {
    if (preg_match('/instagram\\.com\\/p\\/([A-Za-z0-9_-]+)/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Extract Facebook post ID
 */
function getFacebookId($url) {
    if (preg_match('/facebook\\.com\\/(?:[^\\/]+)\\/posts\\/(\\d+)/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/fb\\.com\\/(\\d+)/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Generate embed HTML for different platforms
 */
function generateEmbedHtml($url, $type) {
    switch ($type) {
        case 'youtube':
            $videoId = getYouTubeId($url);
            if ($videoId) {
                return '<div class="embed-container"><iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
            }
            break;
            
        case 'instagram':
            $postId = getInstagramId($url);
            if ($postId) {
                return '<div class="embed-container instagram-embed"><blockquote class="instagram-media" data-instgrm-permalink="' . htmlspecialchars($url) . '" data-instgrm-version="14"></blockquote></div>';
            }
            break;
            
        case 'facebook':
            return '<div class="embed-container facebook-embed"><div class="fb-post" data-href="' . htmlspecialchars($url) . '" data-width="500" data-show-text="true"></div></div>';
            break;
            
        case 'twitter':
            return '<div class="embed-container twitter-embed"><blockquote class="twitter-tweet"><a href="' . htmlspecialchars($url) . '"></a></blockquote></div>';
            break;
            
        case 'tiktok':
            return '<div class="embed-container tiktok-embed"><blockquote class="tiktok-embed" cite="' . htmlspecialchars($url) . '" data-video-id=""><section></section></blockquote></div>';
            break;
            
        case 'vimeo':
            if (preg_match('/vimeo\\.com\\/(\\d+)/', $url, $matches)) {
                return '<div class="embed-container"><iframe src="https://player.vimeo.com/video/' . $matches[1] . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe></div>';
            }
            break;
    }
    
    return '<div class="embed-container"><a href="' . htmlspecialchars($url) . '" target="_blank">View original content</a></div>';
}

/**
 * Get platform name from type
 */
function getPlatformName($type) {
    $platforms = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'twitter' => 'Twitter/X',
        'tiktok' => 'TikTok',
        'vimeo' => 'Vimeo'
    ];
    return $platforms[$type] ?? 'External';
}

/**
 * Get platform-specific CSS classes
 */
function getEmbedClasses($type) {
    $classes = ['embed-wrapper'];
    switch ($type) {
        case 'youtube':
            $classes[] = 'embed-youtube';
            break;
        case 'instagram':
            $classes[] = 'embed-instagram';
            break;
        case 'facebook':
            $classes[] = 'embed-facebook';
            break;
        case 'twitter':
            $classes[] = 'embed-twitter';
            break;
        case 'tiktok':
            $classes[] = 'embed-tiktok';
            break;
    }
    return implode(' ', $classes);
}

/**
 * Get platform icon
 */
function getPlatformIcon($type) {
    $icons = [
        'facebook' => '📘',
        'instagram' => '📷',
        'youtube' => '▶️',
        'twitter' => '🐦',
        'tiktok' => '🎵',
        'vimeo' => '🎬'
    ];
    return $icons[$type] ?? '🔗';
}

/**
 * Fetch embed data from oEmbed endpoints (if available)
 */
function fetchOembedData($url, $type) {
    $oembedEndpoints = [
        'youtube' => 'https://www.youtube.com/oembed?url=',
        'instagram' => 'https://graph.facebook.com/v17.0/instagram_oembed?url=',
        'twitter' => 'https://publish.twitter.com/oembed?url=',
        'vimeo' => 'https://vimeo.com/api/oembed.json?url='
    ];
    
    if (isset($oembedEndpoints[$type])) {
        $apiUrl = $oembedEndpoints[$type] . urlencode($url);
        
        // Try to use file_get_contents if allow_url_fopen is enabled
        if (ini_get('allow_url_fopen')) {
            $response = @file_get_contents($apiUrl);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['title'])) {
                    return $data['title'];
                }
            }
        }
        
        // Try cURL as fallback
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['title'])) {
                    return $data['title'];
                }
            }
        }
    }
    
    return null;
}
?>