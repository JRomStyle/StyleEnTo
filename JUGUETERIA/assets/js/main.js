/**
 * JUGUETERÍA MÁGICA - Interactive Micro-Animations
 * Playful JavaScript interactions for enhanced UX
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Flash messages with auto-dismiss
    const flashMsg = document.getElementById('msg-flash');
    if(flashMsg){
        setTimeout(() => {
            flashMsg.style.transition = 'opacity 0.5s ease';
            flashMsg.style.opacity = '0';
            setTimeout(() => flashMsg.remove(), 500);
        }, 3000);
    }
    
    // ============================================
    // CART ANIMATION ON ADD
    // ============================================
    
    function animateCartBadge() {
        const cartBadge = document.querySelector('.animate-bounce-gentle');
        if (cartBadge) {
            cartBadge.classList.add('animate-wiggle');
            setTimeout(() => cartBadge.classList.remove('animate-wiggle'), 500);
        }
    }
    
    // Add to cart button feedback
    document.querySelectorAll('a[href*="/cart/add/"]').forEach(button => {
        button.addEventListener('click', function(e) {
            this.classList.add('animate-pulse-soft');
            animateCartBadge();
            showToast('¡Producto agregado! 🎉');
        });
    });
    
    // ============================================
    // TOAST NOTIFICATIONS
    // ============================================
    
    function showToast(message, duration = 3000) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = 'toast-notification fixed bottom-8 right-8 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 animate-slide-in-right';
        toast.innerHTML = `<div class="flex items-center space-x-3"><i class="fa fa-check-circle text-2xl"></i><span class="font-bold">${message}</span></div>`;
        
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    // ============================================
    // PRODUCT CARD HOVER EFFECTS
    // ============================================
    
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const rotation = Math.random() > 0.5 ? -2 : 2;
            this.style.transform = `translateY(-12px) rotate(${rotation}deg)`;
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // ============================================
    // CATEGORY CARD TILT
    // ============================================
    
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const rotation = Math.random() * 4 - 2;
            this.style.transform = `scale(1.05) translateY(-5px) rotate(${rotation}deg)`;
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // ============================================
    // SMOOTH SCROLL
    // ============================================
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // ============================================
    // HEADER SHADOW ON SCROLL
    // ============================================
    
    const nav = document.querySelector('nav');
    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.classList.add('shadow-2xl');
                nav.classList.remove('shadow-xl');
            } else {
                nav.classList.add('shadow-xl');
                nav.classList.remove('shadow-2xl');
            }
        });
    }
    
    // ============================================
    // FORM LOADING STATES
    // ============================================
    
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Procesando...';
            }
        });
    });
    
    // ============================================
    // QUANTITY SELECTOR ANIMATION
    // ============================================
    
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('change', function() {
            this.classList.add('animate-pulse-soft');
            setTimeout(() => this.classList.remove('animate-pulse-soft'), 400);
        });
    });
    
});

// Toast notification styles
const style = document.createElement('style');
style.textContent = `.toast-notification { transition: opacity 0.3s ease, transform 0.3s ease; }`;
document.head.appendChild(style);

