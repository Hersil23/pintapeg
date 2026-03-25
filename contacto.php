<!DOCTYPE html>
<html lang="es">
<?php $titulo = 'Contactanos - PintaPeg'; ?>
<?php $metaDesc = 'Contacta a PintaPeg - Ubicacion, delivery en Barquisimeto y formulario de contacto via WhatsApp.'; ?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body>

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <!-- Header -->
  <section class="section section-dark" style="padding-top:120px;padding-bottom:2rem">
    <div class="container">
      <div class="section-title">
        <h2>Contactanos</h2>
        <p>Estamos para ayudarte</p>
      </div>
    </div>
  </section>

  <!-- Contacto -->
  <section class="section">
    <div class="container">
      <div class="contact-grid">
        <!-- Info -->
        <div>
          <h2 style="margin-bottom:1.5rem">Informacion de Contacto</h2>

          <div class="contact-info-item">
            <div class="icon">&#128205;</div>
            <div>
              <strong>Ubicacion</strong>
              <p style="color:var(--gris-oscuro)">Barquisimeto, Estado Lara, Venezuela</p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon">&#128241;</div>
            <div>
              <strong>WhatsApp</strong>
              <p><a href="https://wa.me/584265196026" target="_blank" style="color:var(--naranja)">0426-5196026</a></p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon">&#9993;</div>
            <div>
              <strong>Email</strong>
              <p><a href="mailto:mpintapeg@gmail.com" style="color:var(--naranja)">mpintapeg@gmail.com</a></p>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="icon">&#128666;</div>
            <div>
              <strong>Delivery</strong>
              <p style="color:var(--gris-oscuro)">Servicio de delivery disponible en Barquisimeto y zonas aledanas</p>
            </div>
          </div>
        </div>

        <!-- Formulario -->
        <div>
          <h2 style="margin-bottom:1.5rem">Enviar Mensaje</h2>
          <form class="contact-form" id="contact-form" onsubmit="sendContactMessage(event)">
            <input type="text" id="contact-nombre" class="form-control" placeholder="Tu nombre" required>
            <input type="text" id="contact-asunto" class="form-control" placeholder="Asunto">
            <textarea id="contact-mensaje" class="form-control" placeholder="Tu mensaje..." rows="5" required></textarea>
            <button type="submit" class="btn-hero" style="width:100%;text-align:center">
              Enviar por WhatsApp
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
  <?php include __DIR__ . '/partials/scripts.php'; ?>
  <script>
    function sendContactMessage(e) {
      e.preventDefault();
      const nombre = document.getElementById('contact-nombre').value.trim();
      const asunto = document.getElementById('contact-asunto').value.trim();
      const mensaje = document.getElementById('contact-mensaje').value.trim();

      let text = `Hola PintaPeg!\n\n`;
      text += `Nombre: ${nombre}\n`;
      if (asunto) text += `Asunto: ${asunto}\n`;
      text += `\nMensaje:\n${mensaje}`;

      const encoded = encodeURIComponent(text);
      window.open(`https://wa.me/584265196026?text=${encoded}`, '_blank');
    }
  </script>
</body>
</html>
