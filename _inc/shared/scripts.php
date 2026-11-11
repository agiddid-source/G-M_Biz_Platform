<!-- Shared Global JavaScript for ALAMOS GENERATOR-MS -->
<script>
    // Shopping Cart State Management (Synchronized via localStorage for Ghost's module)
    window.AlamosStore = {
        getCart: function() {
            try {
                return JSON.parse(localStorage.getItem('alamos_cart') || '[]');
            } catch(e) {
                return [];
            }
        },
        saveCart: function(cart) {
            localStorage.setItem('alamos_cart', JSON.stringify(cart));
            this.updateBadge();
        },
        addItem: function(productId, qty = 1, type = 'buy') {
            const cart = this.getCart();
            const existingIndex = cart.findIndex(item => item.id === productId && item.type === type);
            if (existingIndex > -1) {
                cart[existingIndex].qty += qty;
            } else {
                cart.push({ id: productId, qty: qty, type: type, addedAt: new Date().toISOString() });
            }
            this.saveCart(cart);
            this.showToast('Item added to your order cart!', 'success');
        },
        updateBadge: function() {
            const cart = this.getCart();
            const totalCount = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
            const badge = document.getElementById('cartCountBadge');
            if (badge) {
                badge.textContent = totalCount;
                badge.style.display = totalCount > 0 ? 'inline-flex' : 'none';
            }
        },
        showToast: function(message, type = 'success') {
            let container = document.getElementById('alamosToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'alamosToastContainer';
                container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-slate-900 text-white border-l-4 border-amber-500' : 'bg-red-800 text-white';
            toast.className = `${bgClass} shadow-xl rounded-md px-4 py-3 text-sm flex items-center gap-3 transition-all transform duration-300 pointer-events-auto max-w-sm`;
            toast.innerHTML = `
                <i class="fa-solid fa-circle-check text-amber-400"></i>
                <div class="flex-1 font-medium">${message}</div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    };

    // Initialize badge on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        window.AlamosStore.updateBadge();
    });
</script>
