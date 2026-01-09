/**
 * 增强版懒加载
 * 使用 Intersection Observer API
 * 支持渐进式加载、模糊到清晰效果
 */

class PuockLazyLoadEnhanced {
    constructor(options = {}) {
        this.config = {
            rootMargin: '50px 0px',
            threshold: 0.01,
            enableAutoReload: true,
            loadingClass: 'lazy-loading',
            loadedClass: 'lazy-loaded',
            errorClass: 'lazy-error',
            // 并发加载数量限制
            concurrentLoads: 3,
            // 失败重试次数
            retryCount: 2,
            // 是否使用渐进式加载
            progressive: true,
            // 是否使用模糊效果
            blurEffect: true,
            ...options
        };

        this.loadingCount = 0;
        this.loadQueue = [];
        this.observer = null;
        this.images = new WeakMap();
        
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver(
                this.handleIntersection.bind(this),
                {
                    rootMargin: this.config.rootMargin,
                    threshold: this.config.threshold
                }
            );
            
            this.observe();
            
            // 监听 DOM 变化，自动观察新增的图片
            if (this.config.enableAutoReload) {
                this.setupMutationObserver();
            }
        } else {
            // 降级到传统方式
            this.fallbackLoad();
        }
    }

    observe(container = document) {
        const images = container.querySelectorAll('img.lazy[data-src], img[data-lazy="true"]');
        images.forEach(img => {
            if (!this.images.has(img)) {
                this.images.set(img, {
                    retries: 0,
                    loaded: false
                });
                this.observer.observe(img);
            }
        });
    }

    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // 检查是否需要排队
                if (this.loadingCount >= this.config.concurrentLoads) {
                    this.loadQueue.push(img);
                } else {
                    this.loadImage(img);
                }
                
                this.observer.unobserve(img);
            }
        });
    }

    loadImage(img) {
        const imageData = this.images.get(img);
        if (!imageData || imageData.loaded) return;

        this.loadingCount++;
        img.classList.add(this.config.loadingClass);

        const src = img.getAttribute('data-src');
        const srcset = img.getAttribute('data-srcset');

        // 创建新图片对象预加载
        const tempImg = new Image();
        
        // 加载成功处理
        tempImg.onload = () => {
            this.onImageLoaded(img, tempImg, src, srcset);
        };

        // 加载失败处理
        tempImg.onerror = () => {
            this.onImageError(img, src, srcset);
        };

        // 开始加载
        if (srcset) {
            tempImg.srcset = srcset;
        }
        tempImg.src = src;
    }

    onImageLoaded(img, tempImg, src, srcset) {
        // 渐进式加载效果
        if (this.config.progressive && img.classList.contains('lazy-blur')) {
            this.progressiveLoad(img, src, srcset);
        } else {
            // 直接替换
            img.src = src;
            if (srcset) {
                img.srcset = srcset;
            }
            this.finalizeImageLoad(img);
        }

        const imageData = this.images.get(img);
        if (imageData) {
            imageData.loaded = true;
        }

        this.loadingCount--;
        this.processQueue();
    }

    onImageError(img, src, srcset) {
        const imageData = this.images.get(img);
        
        if (imageData && imageData.retries < this.config.retryCount) {
            // 重试加载
            imageData.retries++;
            setTimeout(() => {
                this.loadImage(img);
            }, 1000 * imageData.retries);
        } else {
            // 加载失败
            img.classList.remove(this.config.loadingClass);
            img.classList.add(this.config.errorClass);
            
            // 触发自定义事件
            const event = new CustomEvent('lazyloaderror', { 
                detail: { src, element: img } 
            });
            img.dispatchEvent(event);
            
            this.loadingCount--;
            this.processQueue();
        }
    }

    progressiveLoad(img, src, srcset) {
        // 模糊到清晰的渐进效果
        const currentSrc = img.src;
        
        // 创建一个高清图层
        img.style.position = 'relative';
        
        // 设置过渡效果
        img.style.transition = 'filter 0.3s ease-out';
        
        // 先显示模糊
        if (this.config.blurEffect) {
            img.style.filter = 'blur(10px)';
        }
        
        // 加载完成后移除模糊
        requestAnimationFrame(() => {
            img.src = src;
            if (srcset) {
                img.srcset = srcset;
            }
            
            // 使用双重 RAF 确保浏览器已经处理了新图片
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    img.style.filter = 'blur(0px)';
                    
                    setTimeout(() => {
                        this.finalizeImageLoad(img);
                    }, 300);
                });
            });
        });
    }

    finalizeImageLoad(img) {
        img.classList.remove(this.config.loadingClass);
        img.classList.add(this.config.loadedClass);
        img.removeAttribute('data-src');
        img.removeAttribute('data-srcset');
        img.style.filter = '';
        img.style.transition = '';
        
        // 触发加载完成事件
        const event = new CustomEvent('lazyloaded', { 
            detail: { element: img } 
        });
        img.dispatchEvent(event);
    }

    processQueue() {
        while (this.loadQueue.length > 0 && this.loadingCount < this.config.concurrentLoads) {
            const img = this.loadQueue.shift();
            this.loadImage(img);
        }
    }

    setupMutationObserver() {
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) { // Element node
                            if (node.matches && node.matches('img.lazy[data-src], img[data-lazy="true"]')) {
                                if (!this.images.has(node)) {
                                    this.images.set(node, {
                                        retries: 0,
                                        loaded: false
                                    });
                                    this.observer.observe(node);
                                }
                            }
                            
                            // 检查子元素
                            const lazyImages = node.querySelectorAll('img.lazy[data-src], img[data-lazy="true"]');
                            lazyImages.forEach(img => {
                                if (!this.images.has(img)) {
                                    this.images.set(img, {
                                        retries: 0,
                                        loaded: false
                                    });
                                    this.observer.observe(img);
                                }
                            });
                        }
                    });
                }
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    fallbackLoad() {
        // 不支持 IntersectionObserver 的降级方案
        const images = document.querySelectorAll('img.lazy[data-src], img[data-lazy="true"]');
        images.forEach(img => {
            const src = img.getAttribute('data-src');
            const srcset = img.getAttribute('data-srcset');
            
            if (src) {
                img.src = src;
                img.removeAttribute('data-src');
            }
            if (srcset) {
                img.srcset = srcset;
                img.removeAttribute('data-srcset');
            }
            
            img.classList.add(this.config.loadedClass);
        });
    }

    // 手动触发观察特定容器
    update(container = document) {
        this.observe(container);
    }

    // 销毁观察器
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        this.images = new WeakMap();
        this.loadQueue = [];
    }
}

// 导出到全局
window.PuockLazyLoadEnhanced = PuockLazyLoadEnhanced;

// 自动初始化
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.puockLazyLoader = new PuockLazyLoadEnhanced({
            progressive: true,
            blurEffect: true
        });
    });
} else {
    window.puockLazyLoader = new PuockLazyLoadEnhanced({
        progressive: true,
        blurEffect: true
    });
}
