<!DOCTYPE html>
<html lang="es">
<?php $titulo = 'Tienda - PintaPeg'; ?>
<?php $metaDesc = 'Catalogo completo de pinturas, MDF y materiales para manualidades en Barquisimeto.'; ?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body>

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <!-- Tienda -->
  <section class="section" style="padding-top:100px">
    <div class="container">
      <div class="section-title">
        <h2>Nuestra Tienda</h2>
        <p>Explora nuestro catalogo completo</p>
      </div>

      <!-- Filtro por categorias -->
      <div class="category-filter" id="category-filter">
        <button class="active" data-cat="all">Todos</button>
      </div>

      <!-- Buscador -->
      <div style="max-width:400px;margin:0 auto 2rem">
        <input type="text" id="search-input" class="form-control" placeholder="Buscar productos..." style="border:2px solid var(--gris-medio);border-radius:50px;padding:0.75rem 1.25rem">
      </div>

      <!-- Grid de productos -->
      <div class="products-grid" id="products-grid">
        <p style="text-align:center;color:var(--gris-oscuro);grid-column:1/-1">Cargando productos...</p>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
  <script>
    // Cargar tienda completa
    let allProducts = [];

    document.addEventListener('DOMContentLoaded', async () => {
      // Cargar categorias para el filtro
      const cats = await apiFetch('categorias.php');
      if (cats && cats.length > 0) {
        const filterContainer = document.getElementById('category-filter');
        cats.forEach(cat => {
          const btn = document.createElement('button');
          btn.textContent = cat.nombre;
          btn.dataset.cat = cat.id;
          btn.addEventListener('click', () => filterByCategory(cat.id));
          filterContainer.appendChild(btn);
        });
      }

      // Cargar productos
      const prods = await apiFetch('productos.php');
      if (prods) {
        allProducts = prods;
        renderStore(prods);
      }

      // Buscador
      const searchInput = document.getElementById('search-input');
      let searchTimeout;
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          const term = searchInput.value.toLowerCase().trim();
          if (!term) {
            renderStore(allProducts);
            return;
          }
          const filtered = allProducts.filter(p =>
            p.nombre.toLowerCase().includes(term) ||
            (p.descripcion && p.descripcion.toLowerCase().includes(term))
          );
          renderStore(filtered);
        }, 300);
      });
    });

    function renderStore(products) {
      const grid = document.getElementById('products-grid');
      if (products.length === 0) {
        grid.innerHTML = '<p style="text-align:center;color:var(--gris-oscuro);grid-column:1/-1">No se encontraron productos</p>';
        return;
      }
      grid.innerHTML = products.map(p => productCardHTML(p)).join('');
    }

    function filterByCategory(catId) {
      // Update active button
      document.querySelectorAll('.category-filter button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.cat == catId || (catId === 'all' && btn.dataset.cat === 'all'));
      });

      if (catId === 'all') {
        renderStore(allProducts);
      } else {
        renderStore(allProducts.filter(p => p.categoria_id == catId));
      }
    }

    // "Todos" button
    document.querySelector('[data-cat="all"]')?.addEventListener('click', () => filterByCategory('all'));
  </script>
</body>
</html>
