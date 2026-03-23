/**
 * PintaPeg - Flujo de checkout WhatsApp
 */

// =============================================
// Iniciar checkout - mostrar formulario
// =============================================
function iniciarCheckout() {
  if (carrito.length === 0) return;

  cerrarCarrito();

  // Crear modal de checkout
  const overlay = document.createElement('div');
  overlay.id = 'checkout-overlay';
  overlay.style.cssText = `
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.6);z-index:300;
    display:flex;align-items:center;justify-content:center;
  `;

  overlay.innerHTML = `
    <div style="background:white;border-radius:16px;padding:2rem;width:90%;max-width:450px;max-height:90vh;overflow-y:auto">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <h3 style="font-size:1.2rem">Datos de Entrega</h3>
        <button onclick="cerrarCheckout()" style="background:none;border:none;font-size:1.5rem;cursor:pointer">&times;</button>
      </div>
      <form id="checkout-form" onsubmit="enviarPedido(event)">
        <div style="margin-bottom:1rem">
          <label style="display:block;margin-bottom:0.3rem;font-size:0.9rem;font-weight:500">Nombre completo *</label>
          <input type="text" id="checkout-nombre" required
            style="width:100%;padding:0.6rem 0.75rem;border:2px solid #E0E0E0;border-radius:8px;font-size:0.9rem">
        </div>
        <div style="margin-bottom:1rem">
          <label style="display:block;margin-bottom:0.3rem;font-size:0.9rem;font-weight:500">Direccion de entrega *</label>
          <textarea id="checkout-direccion" required rows="3"
            style="width:100%;padding:0.6rem 0.75rem;border:2px solid #E0E0E0;border-radius:8px;font-size:0.9rem;resize:vertical"
            placeholder="Av., calle, edificio, piso, apto..."></textarea>
        </div>
        <div style="margin-bottom:1.5rem">
          <label style="display:block;margin-bottom:0.3rem;font-size:0.9rem;font-weight:500">Referencia (opcional)</label>
          <input type="text" id="checkout-referencia"
            style="width:100%;padding:0.6rem 0.75rem;border:2px solid #E0E0E0;border-radius:8px;font-size:0.9rem"
            placeholder="Frente a..., al lado de...">
        </div>
        <button type="submit" style="
          display:block;width:100%;padding:0.85rem;background:#25D366;color:white;
          border:none;border-radius:50px;font-weight:700;font-size:1rem;cursor:pointer">
          Enviar Pedido por WhatsApp
        </button>
      </form>
    </div>
  `;

  document.body.appendChild(overlay);
  document.body.style.overflow = 'hidden';

  // Focus en nombre
  setTimeout(() => document.getElementById('checkout-nombre')?.focus(), 100);
}

// =============================================
// Cerrar checkout
// =============================================
function cerrarCheckout() {
  document.getElementById('checkout-overlay')?.remove();
  document.body.style.overflow = '';
}

// =============================================
// Enviar pedido por WhatsApp
// =============================================
async function enviarPedido(e) {
  e.preventDefault();

  const nombre = document.getElementById('checkout-nombre').value.trim();
  const direccion = document.getElementById('checkout-direccion').value.trim();
  const referencia = document.getElementById('checkout-referencia').value.trim();

  if (!nombre || !direccion) return;

  const tasa = getTasaActual();
  const moneda = getMonedaPreferida();

  // Armar lista de productos
  let productosTexto = '';
  let totalUSD = 0;

  carrito.forEach(item => {
    const precioUSD = item.monedaBase === 'usd' ? item.precio : (tasa > 0 ? item.precio / tasa : 0);
    const subtotalUSD = precioUSD * item.cantidad;
    totalUSD += subtotalUSD;

    productosTexto += `- ${item.nombre} x${item.cantidad} — $${subtotalUSD.toFixed(2)}\n`;
  });

  const totalVES = totalUSD * tasa;

  // Armar mensaje WhatsApp
  let mensaje = `Hola PintaPeg, quiero hacer un pedido:\n\n`;
  mensaje += productosTexto;
  mensaje += `\nTotal: $${totalUSD.toFixed(2)} / Bs. ${totalVES.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}\n`;
  mensaje += `\nDatos de entrega:\n`;
  mensaje += `Nombre: ${nombre}\n`;
  mensaje += `Direccion: ${direccion}\n`;
  if (referencia) {
    mensaje += `Referencia: ${referencia}\n`;
  }

  // Registrar venta en BD
  const cartData = getCartData();
  try {
    await fetch('/api/ventas.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        productos: cartData.items,
        total_usd: totalUSD,
        total_ves: totalVES,
        tasa_usada: tasa,
        moneda_cliente: moneda,
        nombre_cliente: nombre,
        direccion: direccion,
        referencia_entrega: referencia,
      }),
    });
  } catch (err) {
    // No bloquear el checkout si falla el registro
    console.error('Error registrando venta:', err);
  }

  // Abrir WhatsApp
  const encoded = encodeURIComponent(mensaje);
  window.open(`https://wa.me/584265196026?text=${encoded}`, '_blank');

  // Limpiar carrito y cerrar
  clearCart();
  cerrarCheckout();

  // Mostrar confirmacion
  showCartToast('Pedido enviado! Te redirigimos a WhatsApp');
}
