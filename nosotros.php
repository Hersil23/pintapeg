<!DOCTYPE html>
<html lang="es">
<?php $titulo = 'Sobre Nosotros - PintaPeg'; ?>
<?php $metaDesc = 'Conoce a PintaPeg - Mision, Vision y Valores. Tu tienda de pinturas y materiales en Barquisimeto.'; ?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body>

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <!-- Header -->
  <section class="section section-dark" style="padding-top:120px">
    <div class="container">
      <div class="section-title">
        <h2>Sobre <span style="color:var(--naranja)">PintaPeg</span></h2>
        <p>Pegale Color a Tu Vida</p>
      </div>
    </div>
  </section>

  <!-- Mision, Vision, Valores -->
  <section class="section">
    <div class="container">
      <div class="values-grid">
        <div class="value-card" style="background:var(--gris-claro);border:none">
          <div class="icon">&#127919;</div>
          <h3 style="color:var(--azul-marino)">Mision</h3>
          <p style="color:var(--gris-oscuro)">Ofrecer productos de calidad para pintura, manualidades y construccion, brindando una experiencia de compra accesible y confiable para nuestros clientes en Barquisimeto y toda Venezuela.</p>
        </div>
        <div class="value-card" style="background:var(--gris-claro);border:none">
          <div class="icon">&#128065;</div>
          <h3 style="color:var(--azul-marino)">Vision</h3>
          <p style="color:var(--gris-oscuro)">Convertirnos en la tienda referencia de pinturas y materiales para manualidades en el estado Lara, reconocida por la calidad de nuestros productos y la excelencia en el servicio.</p>
        </div>
        <div class="value-card" style="background:var(--gris-claro);border:none">
          <div class="icon">&#11088;</div>
          <h3 style="color:var(--azul-marino)">Valores</h3>
          <p style="color:var(--gris-oscuro)">Honestidad, compromiso con el cliente, calidad en cada producto que ofrecemos, innovacion constante y pasion por el color y la creatividad.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section section-dark" style="text-align:center">
    <div class="container">
      <h2 style="margin-bottom:1rem">Listo para darle color a tu proyecto?</h2>
      <p style="color:var(--gris-medio);margin-bottom:2rem">Explora nuestro catalogo y encuentra todo lo que necesitas</p>
      <a href="/tienda.php" class="btn-hero">Ver Catalogo</a>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
