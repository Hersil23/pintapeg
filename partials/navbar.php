<?php $pagina = basename($_SERVER['PHP_SELF']); ?>
<!-- Navbar -->
<nav class="navbar">
  <button class="btn-menu-toggle">&#9776;</button>
  <a href="/" class="logo">
    <img src="/assets/img/logo/logo.png" alt="Materiales PintaPeg">
  </a>
  <div class="nav-right">
    <div class="moneda-selector moneda-desktop">
      <button data-moneda="usd" class="active">USD</button>
      <button data-moneda="ves">Bs</button>
    </div>
    <button class="btn-carrito" onclick="abrirCarrito()">
      &#128722;
      <span class="badge-count" style="display:none">0</span>
    </button>
  </div>
  <div class="nav-links">
    <a href="/index.php" class="<?= $pagina === 'index.php' ? 'active' : '' ?>">Inicio</a>
    <a href="/tienda.php" class="<?= $pagina === 'tienda.php' ? 'active' : '' ?>">Tienda</a>
    <a href="/nosotros.php" class="<?= $pagina === 'nosotros.php' ? 'active' : '' ?>">Nosotros</a>
    <a href="/contacto.php" class="<?= $pagina === 'contacto.php' ? 'active' : '' ?>">Contacto</a>
    <div class="moneda-selector moneda-mobile">
      <button data-moneda="usd" class="active">USD</button>
      <button data-moneda="ves">Bs</button>
    </div>
  </div>
</nav>
