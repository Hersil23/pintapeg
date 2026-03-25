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
            <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
              <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
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

  <!-- Testimonios -->
  <section class="seccion-testimonios">
    <div class="testimonios-container">
      <div class="testimonios-header">
        <h2 class="testimonios-titulo">Lo que dicen nuestros clientes</h2>
        <p class="testimonios-subtitulo">La confianza de Barquisimeto nos respalda</p>
      </div>
      <div class="testimonios-grid">

        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p class="testimonio-texto">"Excelente servicio, los materiales llegaron rapidísimo y con muy buena calidad. El MDF que compré quedó perfecto para mi proyecto. Sin duda volvere a comprar."</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">M</div>
            <div>
              <p class="testimonio-nombre">María González</p>
              <p class="testimonio-cargo">Artesana</p>
            </div>
          </div>
        </div>

        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p class="testimonio-texto">"Llevo meses comprando aquí y siempre encuentro todo lo que necesito. La pega amarilla Cano Fix es la mejor del mercado y a un precio justo. 100% recomendado."</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">C</div>
            <div>
              <p class="testimonio-nombre">Carlos Medina</p>
              <p class="testimonio-cargo">Carpintero</p>
            </div>
          </div>
        </div>

        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p class="testimonio-texto">"El delivery es rapidísimo, pedí en la mañana y me llegó en la tarde. Los productos son originales y el trato es muy amable. PintaPeg es mi tienda favorita."</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">L</div>
            <div>
              <p class="testimonio-nombre">Luisa Pérez</p>
              <p class="testimonio-cargo">Decoradora de interiores</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
