<!DOCTYPE html>
<html lang="es">
<?php $titulo = 'PintaPeg - Pegale Color a Tu Vida'; ?>
<?php $metaDesc = 'Tienda online de pinturas, MDF y materiales para manualidades en Barquisimeto.'; ?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body>

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-bg">
      <div class="hero-content">
        <h1 class="hero-title">
          <span class="hero-line-top">PÉGALE</span>
          <span class="hero-line-color">
            <span style="color:#FF3B30">C</span><span style="color:#FF9500">O</span><span style="color:#FFD700">L</span><span style="color:#34C759">O</span><span style="color:#007AFF">R</span>
            <span class="hero-a">&nbsp;A</span>
          </span>
          <span class="hero-line-bottom">TU VIDA</span>
        </h1>
      </div>
      <div class="hero-overlay"></div>
      <div class="hero-equipo-wrap">
        <img src="/assets/img/hero/equipo.png" alt="Equipo PintaPeg" class="hero-equipo">
      </div>
    </div>
  </section>

  <!-- Productos destacados -->
  <section class="seccion-productos">
    <div class="productos-header">
      <h2 class="productos-titulo">PRODUCTOS DESTACADOS</h2>
    </div>
    <div class="productos-grid" id="featured-products">
      <div class="producto-card">
        <div class="producto-img-wrap">
          <img src="/assets/img/placeholder.png" alt="Producto" class="producto-img">
        </div>
        <div class="producto-info">
          <p class="producto-nombre">Nombre del producto</p>
          <p class="producto-ref">REF.$00</p>
        </div>
        <div class="producto-acciones">
          <a href="#" class="btn-wsp">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="18"> REF.$00
          </a>
          <a href="#" class="btn-comprar">COMPRAR <span class="btn-precio">REF.$00</span></a>
        </div>
      </div>
    </div>
    <div class="productos-ver-todos">
      <a href="/tienda.php" class="btn-ver-todos">VER TODOS</a>
    </div>
  </section>

  <!-- Por que elegirnos -->
  <section class="seccion-elegirnos">
    <div class="elegirnos-container">
      <div class="elegirnos-header">
        <h2 class="elegirnos-titulo">¿Por qué elegirnos?</h2>
        <p class="elegirnos-subtitulo">Tu tienda de confianza en Barquisimeto</p>
      </div>
      <div class="elegirnos-grid">

        <div class="elegir-card">
          <div class="elegir-icono">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
              <circle cx="13.5" cy="6.5" r="2.5"/>
              <circle cx="19" cy="13" r="2.5"/>
              <circle cx="6.5" cy="13" r="2.5"/>
              <circle cx="13.5" cy="19" r="2.5"/>
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="none" fill="none"/>
              <path d="M8.5 8.5l7 7M15.5 8.5l-7 7" stroke="none"/>
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none"/>
            </svg>
          </div>
          <h3 class="elegir-titulo">Variedad</h3>
          <p class="elegir-desc">Amplio catalogo de pinturas, MDF, pegamentos y herramientas para todos tus proyectos creativos.</p>
        </div>

        <div class="elegir-card">
          <div class="elegir-icono">
            <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
              <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
            </svg>
          </div>
          <h3 class="elegir-titulo">Delivery</h3>
          <p class="elegir-desc">Entregamos en Barquisimeto y zonas aledanas. Tu pedido llega rapido y seguro a tu puerta.</p>
        </div>

        <div class="elegir-card">
          <div class="elegir-icono">
            <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
              <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
            </svg>
          </div>
          <h3 class="elegir-titulo">Precios Justos</h3>
          <p class="elegir-desc">Precios competitivos en USD y Bolivares. Tasa BCV actualizada diariamente para tu comodidad.</p>
        </div>

      </div>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
