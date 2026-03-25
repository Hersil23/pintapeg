/**
 * PintaPeg - Script principal
 */

const API_URL = '/api';

// =============================================
// Service Worker
// =============================================
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .catch(err => console.log('SW registro fallido:', err));
  });
}

// =============================================
// Mobile menu toggle
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.querySelector('.btn-menu-toggle');
  const navbar = document.querySelector('.navbar');

  if (menuBtn && navbar) {
    menuBtn.addEventListener('click', () => {
      navbar.classList.toggle('menu-abierto');
    });

    // Cerrar al hacer click en un link
    const navLinks = navbar.querySelector('.nav-links');
    if (navLinks) {
      navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          navbar.classList.remove('menu-abierto');
        });
      });
    }
  }

});

// =============================================
// Navbar scroll effect
// =============================================
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.style.background = 'linear-gradient(to right, #5a5a54, #d43020, #e04e0d, #e07415, #e0701a)';
      navbar.style.backdropFilter = 'blur(10px)';
    } else {
      navbar.style.background = 'linear-gradient(to right, #706F69, #fe3a28, #ff5f10, #ff8719, #ff821f)';
      navbar.style.backdropFilter = 'none';
    }
  }
});

// =============================================
// Fetch API helper
// =============================================
async function apiFetch(endpoint) {
  try {
    const res = await fetch(`${API_URL}/${endpoint}`);
    return await res.json();
  } catch (err) {
    console.error('API Error:', err);
    return null;
  }
}

// =============================================
// Formatear precio
// =============================================
function formatPrice(amount, currency = 'usd') {
  const num = parseFloat(amount);
  if (currency === 'ves') {
    return 'Bs. ' + num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  return '$' + num.toFixed(2);
}

// =============================================
// Cargar productos destacados (index.html)
// =============================================
async function loadFeaturedProducts() {
  const container = document.getElementById('featured-products');
  if (!container) return;

  const productos = await apiFetch('productos.php?destacados=1');
  if (!productos || productos.length === 0) {
    container.innerHTML = '<p style="text-align:center;color:var(--gris-oscuro)">Proximamente...</p>';
    return;
  }

  container.innerHTML = productos.map(p => productCardHTML(p)).join('');
}

// =============================================
// Generar HTML de product card
// =============================================
function productCardHTML(prod) {
  const moneda = getMonedaPreferida();
  const tasa = getTasaActual();
  const precioDisplay = calcularPrecio(prod.precio, prod.moneda_base, moneda, tasa);
  const precioAlt = calcularPrecio(prod.precio, prod.moneda_base, moneda === 'usd' ? 'ves' : 'usd', tasa);
  const monedaAlt = moneda === 'usd' ? 'ves' : 'usd';

  const imgSrc = prod.imagen ? `/uploads/productos/${prod.imagen}` : '/assets/img/icons/icon-192x192.png';

  return `
    <div class="product-card" data-id="${prod.id}" data-categoria="${prod.categoria_id}">
      ${prod.destacado ? '<span class="badge-destacado">Destacado</span>' : ''}
      <a href="/producto.html?slug=${prod.slug}">
        <div class="product-card-img">
          <img src="${imgSrc}" alt="${prod.nombre}" loading="lazy">
        </div>
      </a>
      <div class="product-card-body">
        <span class="category">${prod.categoria_nombre || ''}</span>
        <h3><a href="/producto.html?slug=${prod.slug}">${prod.nombre}</a></h3>
        <div class="price" data-precio="${prod.precio}" data-moneda-base="${prod.moneda_base}">
          ${formatPrice(precioDisplay, moneda)}
        </div>
        <div class="price-alt">${formatPrice(precioAlt, monedaAlt)}</div>
        <button class="btn-add" onclick="addToCart(${prod.id}, '${prod.nombre.replace(/'/g, "\\'")}', ${prod.precio}, '${prod.moneda_base}', '${imgSrc}')">
          Agregar al carrito
        </button>
      </div>
    </div>
  `;
}

// =============================================
// Calcular precio con conversion
// =============================================
function calcularPrecio(precio, monedaBase, monedaDestino, tasa) {
  precio = parseFloat(precio);
  tasa = parseFloat(tasa) || 1;

  if (monedaBase === monedaDestino) return precio;

  if (monedaBase === 'usd' && monedaDestino === 'ves') {
    return precio * tasa;
  }

  if (monedaBase === 'ves' && monedaDestino === 'usd') {
    return tasa > 0 ? precio / tasa : 0;
  }

  return precio;
}

// =============================================
// Init en DOMContentLoaded
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  loadFeaturedProducts();
});
