<!DOCTYPE html>
<html lang="es">
<?php $titulo = 'Producto - PintaPeg'; ?>
<?php $metaDesc = 'Detalle de producto - PintaPeg'; ?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body>

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <!-- Detalle -->
  <section class="product-detail">
    <div class="container" id="product-container">
      <p style="text-align:center;color:var(--gris-oscuro);grid-column:1/-1;padding:4rem 0">Cargando producto...</p>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
  <script>
    let currentProduct = null;
    let qty = 1;

    document.addEventListener('DOMContentLoaded', async () => {
      const params = new URLSearchParams(window.location.search);
      const slug = params.get('slug');

      if (!slug) {
        window.location.href = '/tienda.php';
        return;
      }

      const prod = await apiFetch(`productos.php?slug=${slug}`);
      if (!prod || prod.error) {
        document.getElementById('product-container').innerHTML =
          '<p style="text-align:center;padding:4rem 0">Producto no encontrado. <a href="/tienda.php" style="color:var(--naranja)">Volver a la tienda</a></p>';
        return;
      }

      currentProduct = prod;
      document.title = `${prod.nombre} - PintaPeg`;

      // Update meta
      const metaDesc = document.querySelector('meta[name="description"]');
      if (metaDesc) metaDesc.content = `${prod.nombre} - ${prod.descripcion || 'PintaPeg'}`;

      renderProductDetail(prod);
    });

    function renderProductDetail(prod) {
      const moneda = getMonedaPreferida();
      const tasa = getTasaActual();
      const monedaAlt = moneda === 'usd' ? 'ves' : 'usd';
      const precioMain = calcularPrecio(prod.precio, prod.moneda_base, moneda, tasa);
      const precioSec = calcularPrecio(prod.precio, prod.moneda_base, monedaAlt, tasa);
      const imgSrc = prod.imagen ? `/uploads/productos/${prod.imagen}` : '/assets/img/icons/icon-192x192.png';

      document.getElementById('product-container').innerHTML = `
        <div class="product-detail-img">
          <img src="${imgSrc}" alt="${prod.nombre}">
        </div>
        <div class="product-detail-info">
          <span class="category">${prod.categoria_nombre || ''}</span>
          <h1>${prod.nombre}</h1>
          <div class="price-main" data-precio="${prod.precio}" data-moneda-base="${prod.moneda_base}">
            ${formatPrice(precioMain, moneda)}
          </div>
          <div class="price-secondary">${formatPrice(precioSec, monedaAlt)}</div>
          ${prod.descripcion ? `<p class="description">${prod.descripcion}</p>` : ''}
          <div class="stock-info">
            ${prod.stock > 0
              ? `<span class="in-stock">En stock (${prod.stock} disponibles)</span>`
              : `<span class="out-stock">Agotado</span>`}
          </div>
          ${prod.stock > 0 ? `
            <div class="quantity-selector">
              <button onclick="changeQty(-1)">-</button>
              <input type="number" id="qty-input" value="1" min="1" max="${prod.stock}" onchange="setQty(this.value)">
              <button onclick="changeQty(1)">+</button>
            </div>
            <button class="btn-add-detail" onclick="addProductToCart()">
              Agregar al carrito
            </button>
          ` : ''}
        </div>
      `;

      // Schema.org JSON-LD
      const schema = {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: prod.nombre,
        description: prod.descripcion || '',
        image: window.location.origin + imgSrc,
        offers: {
          '@type': 'Offer',
          price: prod.precio,
          priceCurrency: prod.moneda_base.toUpperCase(),
          availability: prod.stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        }
      };
      const scriptTag = document.createElement('script');
      scriptTag.type = 'application/ld+json';
      scriptTag.textContent = JSON.stringify(schema);
      document.head.appendChild(scriptTag);
    }

    function changeQty(delta) {
      const input = document.getElementById('qty-input');
      const max = parseInt(input.max) || 99;
      qty = Math.min(Math.max(1, qty + delta), max);
      input.value = qty;
    }

    function setQty(val) {
      const input = document.getElementById('qty-input');
      const max = parseInt(input.max) || 99;
      qty = Math.min(Math.max(1, parseInt(val) || 1), max);
      input.value = qty;
    }

    function addProductToCart() {
      if (!currentProduct) return;
      const imgSrc = currentProduct.imagen ? `/uploads/productos/${currentProduct.imagen}` : '/assets/img/icons/icon-192x192.png';

      for (let i = 0; i < qty; i++) {
        addToCart(currentProduct.id, currentProduct.nombre, currentProduct.precio, currentProduct.moneda_base, imgSrc);
      }
    }
  </script>
</body>
</html>
