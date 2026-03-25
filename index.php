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
  <section class="section">
    <div class="container">
      <div class="section-title">
        <h2>Productos Destacados</h2>
        <p>Lo mejor de nuestra tienda, seleccionado para ti</p>
      </div>
      <div class="products-grid" id="featured-products">
        <p style="text-align:center;color:var(--gris-oscuro);grid-column:1/-1">Cargando productos...</p>
      </div>
      <div style="text-align:center;margin-top:2rem">
        <a href="/tienda.php" class="btn-hero">Ver Todos los Productos</a>
      </div>
    </div>
  </section>

  <!-- Por que PintaPeg -->
  <section class="section section-dark">
    <div class="container">
      <div class="section-title">
        <h2>Por que elegirnos</h2>
        <p>Tu tienda de confianza en Barquisimeto</p>
      </div>
      <div class="values-grid">
        <div class="value-card">
          <div class="icon">&#127912;</div>
          <h3>Variedad</h3>
          <p>Amplio catalogo de pinturas, MDF, pegamentos y herramientas para todos tus proyectos.</p>
        </div>
        <div class="value-card">
          <div class="icon">&#128666;</div>
          <h3>Delivery</h3>
          <p>Entregamos en Barquisimeto y zonas aledanas. Tu pedido llega rapido y seguro.</p>
        </div>
        <div class="value-card">
          <div class="icon">&#128176;</div>
          <h3>Precios Justos</h3>
          <p>Precios competitivos en USD y Bolivares. Tasa actualizada diariamente.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA WhatsApp -->
  <section class="section" style="text-align:center">
    <div class="container">
      <h2 style="font-size:2rem;margin-bottom:1rem">Tienes alguna pregunta?</h2>
      <p style="color:var(--gris-oscuro);margin-bottom:2rem;font-size:1.1rem">Escribenos por WhatsApp y te atendemos al instante</p>
      <a href="https://wa.me/584265196026" target="_blank" class="btn-whatsapp">
        &#128172; Escribenos por WhatsApp
      </a>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
