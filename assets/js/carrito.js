/**
 * PintaPeg - Carrito de compras (drawer lateral)
 */

let carrito = [];

// =============================================
// Inicializar carrito
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  cargarCarrito();
  actualizarCarritoUI();
});

// =============================================
// Persistencia en localStorage
// =============================================
function cargarCarrito() {
  const data = localStorage.getItem('pintapeg_carrito');
  carrito = data ? JSON.parse(data) : [];
}

function guardarCarrito() {
  localStorage.setItem('pintapeg_carrito', JSON.stringify(carrito));
}

// =============================================
// Agregar al carrito
// =============================================
function addToCart(id, nombre, precio, monedaBase, imagen) {
  const existente = carrito.find(item => item.id === id);

  if (existente) {
    existente.cantidad++;
  } else {
    carrito.push({ id, nombre, precio: parseFloat(precio), monedaBase, imagen, cantidad: 1 });
  }

  guardarCarrito();
  actualizarCarritoUI();
  abrirCarrito();

  // Feedback visual
  showCartToast(`${nombre} agregado al carrito`);
}

// =============================================
// Modificar cantidad
// =============================================
function updateCartQty(id, delta) {
  const item = carrito.find(i => i.id === id);
  if (!item) return;

  item.cantidad += delta;
  if (item.cantidad <= 0) {
    carrito = carrito.filter(i => i.id !== id);
  }

  guardarCarrito();
  actualizarCarritoUI();
}

function removeFromCart(id) {
  carrito = carrito.filter(i => i.id !== id);
  guardarCarrito();
  actualizarCarritoUI();
}

// =============================================
// UI del carrito
// =============================================
function actualizarCarritoUI() {
  // Badge count
  const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
  document.querySelectorAll('.badge-count').forEach(el => {
    el.textContent = totalItems;
    el.style.display = totalItems > 0 ? 'flex' : 'none';
  });

  // Drawer items
  const itemsContainer = document.querySelector('.cart-items');
  if (!itemsContainer) return;

  const moneda = getMonedaPreferida();
  const tasa = getTasaActual();
  const monedaAlt = moneda === 'usd' ? 'ves' : 'usd';

  if (carrito.length === 0) {
    itemsContainer.innerHTML = '<div class="cart-empty"><p>Tu carrito esta vacio</p></div>';
  } else {
    itemsContainer.innerHTML = carrito.map(item => {
      const precioUnit = calcularPrecio(item.precio, item.monedaBase, moneda, tasa);
      const subtotal = precioUnit * item.cantidad;

      return `
        <div class="cart-item">
          <img src="${item.imagen}" alt="${item.nombre}">
          <div class="cart-item-info">
            <h4>${item.nombre}</h4>
            <div class="cart-item-price">${formatPrice(subtotal, moneda)}</div>
            <div class="cart-item-qty">
              <button onclick="updateCartQty(${item.id}, -1)">-</button>
              <span>${item.cantidad}</span>
              <button onclick="updateCartQty(${item.id}, 1)">+</button>
            </div>
            <button class="cart-item-remove" onclick="removeFromCart(${item.id})">Eliminar</button>
          </div>
        </div>
      `;
    }).join('');
  }

  // Totales
  const totalUSD = carrito.reduce((sum, item) => {
    const precioUSD = item.monedaBase === 'usd' ? item.precio : (tasa > 0 ? item.precio / tasa : 0);
    return sum + (precioUSD * item.cantidad);
  }, 0);

  const totalVES = totalUSD * tasa;

  const totalDisplay = moneda === 'usd' ? formatPrice(totalUSD, 'usd') : formatPrice(totalVES, 'ves');
  const totalAlt = moneda === 'usd' ? formatPrice(totalVES, 'ves') : formatPrice(totalUSD, 'usd');

  const totalEl = document.querySelector('.total-value');
  if (totalEl) totalEl.textContent = totalDisplay;

  const totalAltEl = document.querySelector('.cart-total-alt');
  if (totalAltEl) totalAltEl.textContent = totalAlt;

  // Boton checkout
  const checkoutBtn = document.querySelector('.btn-checkout');
  if (checkoutBtn) {
    checkoutBtn.disabled = carrito.length === 0;
    checkoutBtn.style.opacity = carrito.length === 0 ? '0.5' : '1';
  }
}

// =============================================
// Abrir/cerrar drawer
// =============================================
function abrirCarrito() {
  document.querySelector('.cart-overlay')?.classList.add('active');
  document.querySelector('.cart-drawer')?.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function cerrarCarrito() {
  document.querySelector('.cart-overlay')?.classList.remove('active');
  document.querySelector('.cart-drawer')?.classList.remove('open');
  document.body.style.overflow = '';
}

// Event listener para cerrar con overlay
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('.cart-overlay')?.addEventListener('click', cerrarCarrito);
  document.querySelector('.cart-close')?.addEventListener('click', cerrarCarrito);
});

// =============================================
// Toast mini para carrito
// =============================================
function showCartToast(msg) {
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%);
    background: var(--azul-marino); color: white; padding: 0.75rem 1.5rem;
    border-radius: 50px; font-size: 0.9rem; z-index: 300;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 2000);
}

// =============================================
// Obtener datos del carrito para checkout
// =============================================
function getCartData() {
  const tasa = getTasaActual();

  const totalUSD = carrito.reduce((sum, item) => {
    const precioUSD = item.monedaBase === 'usd' ? item.precio : (tasa > 0 ? item.precio / tasa : 0);
    return sum + (precioUSD * item.cantidad);
  }, 0);

  return {
    items: carrito.map(item => ({
      id: item.id,
      nombre: item.nombre,
      precio: item.precio,
      moneda_base: item.monedaBase,
      cantidad: item.cantidad,
      subtotal: item.precio * item.cantidad,
    })),
    total_usd: totalUSD,
    total_ves: totalUSD * tasa,
    tasa_usada: tasa,
    moneda_cliente: getMonedaPreferida(),
  };
}

// Limpiar carrito despues de checkout
function clearCart() {
  carrito = [];
  guardarCarrito();
  actualizarCarritoUI();
}
