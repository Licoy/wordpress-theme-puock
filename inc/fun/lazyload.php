<?php
/**
 * 增强版图片懒加载功能
 * 支持 WebP、AVIF、占位符、响应式图片
 */

// 检测浏览器是否支持 WebP
function pk_browser_supports_webp() {
    static $supports_webp = null;
    
    if ($supports_webp !== null) {
        return $supports_webp;
    }
    
    // 检查 HTTP Accept 头
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
        $supports_webp = true;
        return true;
    }
    
    // 检查 Cookie（用于客户端检测后设置）
    if (isset($_COOKIE['webp_support'])) {
        $supports_webp = $_COOKIE['webp_support'] === '1';
        return $supports_webp;
    }
    
    $supports_webp = false;
    return false;
}

// 检测浏览器是否支持 AVIF
function pk_browser_supports_avif() {
    static $supports_avif = null;
    
    if ($supports_avif !== null) {
        return $supports_avif;
    }
    
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false) {
        $supports_avif = true;
        return true;
    }
    
    if (isset($_COOKIE['avif_support'])) {
        $supports_avif = $_COOKIE['avif_support'] === '1';
        return $supports_avif;
    }
    
    $supports_avif = false;
    return false;
}

// 获取优化后的图片URL（支持WebP/AVIF）
function pk_get_optimized_image_url($src, $width = null, $height = null) {
    if (empty($src)) {
        return $src;
    }
    
    // 如果已经是 WebP 或 AVIF，直接返回
    $ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION));
    if (in_array($ext, ['webp', 'avif'])) {
        return $src;
    }
    
    // 优先使用 AVIF（更高压缩率）
    if (pk_is_checked('lazyload_avif_support') && pk_browser_supports_avif()) {
        $avif_src = preg_replace('/\.(jpg|jpeg|png)$/i', '.avif', $src);
        // 这里可以添加检查文件是否存在的逻辑
        // 实际应用中，建议使用图片服务或CDN自动转换
    }
    
    // 其次使用 WebP
    if (pk_is_checked('lazyload_webp_support') && pk_browser_supports_webp()) {
        $webp_src = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src);
        // 如果使用CDN，可以返回转换后的URL
        // 例如：return $src . '?format=webp';
    }
    
    return $src;
}

// 生成 LQIP (Low Quality Image Placeholder) - 低质量占位符
function pk_get_lqip_placeholder($src, $quality = 10, $blur = 10) {
    if (!pk_is_checked('lazyload_lqip_enable')) {
        return pk_get_lazy_pl_img();
    }
    
    // 检查图片 URL 是否为空
    if (empty($src) || $src === 'null' || $src === null) {
        return pk_get_lazy_pl_img();
    }
    
    // 使用 timthumb 生成低质量占位图
    $placeholder = PUOCK_ABS_URI . "/timthumb.php?w=50&h=50&q={$quality}&f={$blur}&src=" . urlencode($src);
    return $placeholder;
}

// 生成响应式图片 srcset
function pk_generate_srcset($src, $sizes = [320, 640, 960, 1280, 1920]) {
    if (!pk_is_checked('lazyload_srcset_enable') || empty($src)) {
        return '';
    }
    
    $srcset = [];
    foreach ($sizes as $size) {
        $url = pk_get_img_thumbnail_src($src, $size, null);
        $url = pk_get_optimized_image_url($url);
        $srcset[] = "{$url} {$size}w";
    }
    
    return implode(', ', $srcset);
}

// 增强版懒加载图片信息获取
function pk_get_enhanced_lazy_img_info($origin, $class = '', $width = null, $height = null, $thumbnail = true, $options = []) {
    // 检查图片 URL 是否为空
    if (empty($origin) || $origin === 'null' || $origin === null) {
        // 返回占位符图片
        return "src='" . pk_get_lazy_pl_img() . "' class='" . esc_attr($class) . "' alt='placeholder'";
    }
    
    $defaults = [
        'use_lqip' => pk_is_checked('lazyload_lqip_enable'),
        'use_srcset' => pk_is_checked('lazyload_srcset_enable'),
        'loading' => 'lazy', // native lazy loading
        'fade_in' => true,
        'blur_up' => true,
    ];
    
    $options = array_merge($defaults, $options);
    
    // 如果未启用懒加载
    if (!pk_is_checked('basic_img_lazy_s')) {
        $src = $thumbnail ? pk_get_img_thumbnail_src($origin, $width, $height) : $origin;
        $src = pk_get_optimized_image_url($src, $width, $height);
        
        $out = "src='{$src}' ";
        $out .= "class='{$class}' ";
        $out .= "loading='{$options['loading']}' ";
        
        if ($options['use_srcset'] && !$thumbnail) {
            $srcset = pk_generate_srcset($origin);
            if ($srcset) {
                $out .= "srcset='{$srcset}' ";
                $out .= "sizes='(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px' ";
            }
        }
        
        return $out;
    }
    
    // 启用了懒加载
    $data_src = $thumbnail ? pk_get_img_thumbnail_src($origin, $width, $height) : $origin;
    $data_src = pk_get_optimized_image_url($data_src, $width, $height);
    
    // 占位符
    $placeholder = $options['use_lqip'] ? pk_get_lqip_placeholder($origin) : pk_get_lazy_pl_img();
    
    $classes = ['lazy'];
    if ($options['fade_in']) {
        $classes[] = 'lazy-fade';
    }
    if ($options['blur_up'] && $options['use_lqip']) {
        $classes[] = 'lazy-blur';
    }
    if ($class) {
        $classes[] = $class;
    }
    
    $out = "src='{$placeholder}' ";
    $out .= "class='" . implode(' ', $classes) . "' ";
    $out .= "data-src='{$data_src}' ";
    $out .= "loading='lazy' "; // 原生懒加载作为后备
    
    // 添加响应式图片支持
    if ($options['use_srcset'] && !$thumbnail) {
        $srcset = pk_generate_srcset($origin);
        if ($srcset) {
            $out .= "data-srcset='{$srcset}' ";
            $out .= "data-sizes='auto' ";
        }
    }
    
    // 添加尺寸信息避免布局抖动
    if ($width && $height) {
        $out .= "width='{$width}' height='{$height}' ";
    }
    
    return $out;
}

// 内容图片懒加载增强
function pk_content_img_lazy_enhanced($content) {
    if (!pk_is_checked('basic_img_lazy_z')) {
        return $content;
    }
    
    $placeholder = pk_is_checked('lazyload_lqip_enable') ? 
        "data-lazy=\"true\" class=\"lazy lazy-fade\"" : 
        "data-lazy=\"true\" class=\"lazy\"";
    
    // 匹配所有 img 标签
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) use ($placeholder) {
            $img = $matches[0];
            $attrs = $matches[1];
            
            // 如果已经有 data-src或data-lazy，说明已经处理过，跳过
            if (strpos($attrs, 'data-src') !== false || strpos($attrs, 'data-lazy') !== false) {
                return $img;
            }
            
            // 提取 src
            if (preg_match('/src=[\'"]([^\'"]+)[\'"]/i', $attrs, $src_match)) {
                $src = $src_match[1];
                
                // 如果 src 已经是占位符图片，跳过（防止重复处理）
                $placeholder_path = '/assets/img/z/load.svg';
                if (strpos($src, $placeholder_path) !== false) {
                    return $img;
                }
                
                // 检查 src 是否为空或 null
                if (empty($src) || $src === 'null' || $src === null || trim($src) === '') {
                    // 如果 src 无效，用占位符替换整个 img 标签
                    $lazy_placeholder = pk_get_lazy_pl_img();
                    $new_attrs = preg_replace(
                        '/src=[\'"]([^\'"]+)[\'"]/i',
                        "src=\"{$lazy_placeholder}\"",
                        $attrs
                    );
                    return "<img{$new_attrs}>";
                }
                
                $lazy_placeholder = pk_get_lazy_pl_img();
                
                // 替换 src 并添加 data-src
                $new_attrs = preg_replace(
                    '/src=[\'"]([^\'"]+)[\'"]/i',
                    "src=\"{$lazy_placeholder}\" data-src=\"{$src}\"",
                    $attrs
                );
                
                // 添加懒加载类和属性
                if (strpos($new_attrs, 'class=') !== false) {
                    $new_attrs = preg_replace('/class=[\'"]([^\'"]*)[\'"]/', 'class="$1 lazy lazy-fade"', $new_attrs);
                } else {
                    $new_attrs .= ' class="lazy lazy-fade"';
                }
                
                $new_attrs .= ' data-lazy="true" loading="lazy"';
                
                return "<img{$new_attrs}>";
            }
            
            return $img;
        },
        $content
    );
    
    return $content;
}

// 替换原有的懒加载过滤器（旧的已在 core.php 中注释）
if (pk_is_checked('basic_img_lazy_z')) {
    add_filter('the_content', 'pk_content_img_lazy_enhanced');
}

// 添加图片格式支持检测脚本
function pk_add_image_format_detection() {
    ?>
    <script>
    // 检测 WebP 支持
    (function() {
        var webp = new Image();
        webp.onload = webp.onerror = function() {
            document.cookie = 'webp_support=' + (webp.height === 2 ? '1' : '0') + '; path=/; max-age=31536000';
        };
        webp.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        
        // 检测 AVIF 支持
        var avif = new Image();
        avif.onload = avif.onerror = function() {
            document.cookie = 'avif_support=' + (avif.height === 2 ? '1' : '0') + '; path=/; max-age=31536000';
        };
        avif.src = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgANogQEAwgMg8f8D///8WfhwB8+ErK42A=';
    })();
    </script>
    <?php
}

// 仅在启用了现代图片格式时添加检测
if (pk_is_checked('lazyload_webp_support') || pk_is_checked('lazyload_avif_support')) {
    add_action('wp_head', 'pk_add_image_format_detection', 1);
}

// 后台选项已在其他文件中定义，这里添加默认值
function pk_lazyload_default_options() {
    return [
        'lazyload_webp_support' => false,
        'lazyload_avif_support' => false,
        'lazyload_lqip_enable' => false,
        'lazyload_srcset_enable' => false,
    ];
}
