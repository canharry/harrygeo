import './bootstrap';

/**
 * Hero 横幅樱花/花瓣飘落动画
 * 仅当页面存在 #heroCanvas 时初始化
 */
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('heroCanvas');
    const hero = document.getElementById('heroBanner');

    // 如果当前页面没有 Hero 区域则直接退出
    if (!canvas || !hero) return;

    const ctx = canvas.getContext('2d');
    let width, height;
    let petals = [];

    // 花瓣基础配置
    const config = {
        count: 45,        // 花瓣数量
        minSize: 6,       // 最小尺寸
        maxSize: 14,      // 最大尺寸
        minSpeed: 0.5,    // 最小下落速度
        maxSpeed: 1.8,    // 最大下落速度
        colors: [
            'rgba(255, 255, 255, 0.85)',
            'rgba(255, 228, 235, 0.85)',
            'rgba(255, 200, 220, 0.8)',
            'rgba(255, 240, 245, 0.75)',
        ],
    };

    /**
     * 初始化画布尺寸
     */
    function resize() {
        const rect = hero.getBoundingClientRect();
        width = canvas.width = rect.width;
        height = canvas.height = rect.height;
    }

    /**
     * 创建一片花瓣
     */
    function createPetal() {
        const size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        return {
            x: Math.random() * width,
            y: Math.random() * -height, // 从屏幕上方外开始
            size: size,
            speed: Math.random() * (config.maxSpeed - config.minSpeed) + config.minSpeed,
            sway: Math.random() * 2 + 0.5,      // 左右摇摆幅度
            swaySpeed: Math.random() * 0.02 + 0.01, // 摇摆速度
            rotation: Math.random() * 360,
            rotationSpeed: (Math.random() - 0.5) * 2,
            color: config.colors[Math.floor(Math.random() * config.colors.length)],
            phase: Math.random() * Math.PI * 2,
        };
    }

    /**
     * 初始化花瓣数组
     */
    function initPetals() {
        petals = [];
        for (let i = 0; i < config.count; i++) {
            const petal = createPetal();
            // 让部分花瓣初始分布在屏幕内，避免等待过久
            petal.y = Math.random() * height;
            petals.push(petal);
        }
    }

    /**
     * 绘制单瓣樱花
     */
    function drawPetal(petal) {
        ctx.save();
        ctx.translate(petal.x, petal.y);
        ctx.rotate((petal.rotation * Math.PI) / 180);
        ctx.fillStyle = petal.color;
        ctx.beginPath();

        // 绘制五瓣樱花轮廓
        const step = (Math.PI * 2) / 5;
        for (let i = 0; i < 5; i++) {
            const angle = i * step;
            const x = Math.cos(angle) * petal.size;
            const y = Math.sin(angle) * petal.size;
            const cpX = Math.cos(angle + step / 2) * (petal.size * 0.5);
            const cpY = Math.sin(angle + step / 2) * (petal.size * 0.5);

            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.quadraticCurveTo(cpX, cpY, x, y);
            }
        }
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    /**
     * 动画主循环
     */
    function animate() {
        ctx.clearRect(0, 0, width, height);

        petals.forEach(function (petal) {
            // 更新位置
            petal.y += petal.speed;
            petal.x += Math.sin(petal.phase) * petal.sway * 0.3;
            petal.phase += petal.swaySpeed;
            petal.rotation += petal.rotationSpeed;

            // 超出底部后重置到顶部
            if (petal.y > height + petal.size) {
                Object.assign(petal, createPetal());
            }

            drawPetal(petal);
        });

        requestAnimationFrame(animate);
    }

    // 初始化并开始动画
    resize();
    initPetals();
    animate();

    // 窗口大小变化时重新计算画布尺寸
    window.addEventListener('resize', function () {
        resize();
    });
});

/**
 * 文章点赞按钮交互
 * 通过 AJAX 提交点赞，成功后更新页面上的点赞数
 */
document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.querySelector('.like-btn');
    if (!likeBtn) return;

    likeBtn.addEventListener('click', function () {
        const url = likeBtn.dataset.likeUrl;
        if (!url) return;

        // 防止重复点击
        if (likeBtn.disabled) return;
        likeBtn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                const countEl = likeBtn.querySelector('.like-count');
                if (countEl && data.likes !== undefined) {
                    countEl.textContent = data.likes;
                }
                alert(data.message || '操作完成');
            })
            .catch(function () {
                alert('点赞失败，请稍后重试');
            })
            .finally(function () {
                likeBtn.disabled = false;
            });
    });
});



