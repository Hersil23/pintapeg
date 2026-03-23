/**
 * PintaPeg - Sistema de moneda USD/Bs
 */

let tasaData = null;

// =============================================
// Inicializar moneda
// =============================================
document.addEventListener('DOMContentLoaded', async () => {
  await cargarTasa();
  initMonedaSelector();
  actualizarPrecios();
});

// =============================================
// Cargar tasa desde API
// =============================================
async function cargarTasa() {
  try {
    const data = await apiFetch('tasa.php');
    if (data) {
      tasaData = data;
      localStorage.setItem('pintapeg_tasa', JSON.stringify(data));
      localStorage.setItem('pintapeg_tasa_ts', Date.now().toString());
    }
  } catch (err) {
    // Intentar desde cache
    const cached = localStorage.getItem('pintapeg_tasa');
    if (cached) {
      tasaData = JSON.parse(cached);
    }
  }
}

// =============================================
// Obtener tasa actual
// =============================================
function getTasaActual() {
  if (!tasaData) return 1;
  return tasaData.valor_activo || 1;
}

// =============================================
// Moneda preferida del usuario
// =============================================
function getMonedaPreferida() {
  return localStorage.getItem('pintapeg_moneda') || 'usd';
}

function setMonedaPreferida(moneda) {
  localStorage.setItem('pintapeg_moneda', moneda);
  actualizarPrecios();
  actualizarCarritoUI();
}

// =============================================
// Inicializar selector de moneda
// =============================================
function initMonedaSelector() {
  const selectors = document.querySelectorAll('.moneda-selector');
  const monedaActual = getMonedaPreferida();

  selectors.forEach(selector => {
    selector.querySelectorAll('button').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.moneda === monedaActual);
      btn.addEventListener('click', () => {
        setMonedaPreferida(btn.dataset.moneda);
        // Update all selectors
        document.querySelectorAll('.moneda-selector button').forEach(b => {
          b.classList.toggle('active', b.dataset.moneda === btn.dataset.moneda);
        });
      });
    });
  });
}

// =============================================
// Actualizar todos los precios en la pagina
// =============================================
function actualizarPrecios() {
  const moneda = getMonedaPreferida();
  const tasa = getTasaActual();
  const monedaAlt = moneda === 'usd' ? 'ves' : 'usd';

  // Actualizar precios en cards
  document.querySelectorAll('.price[data-precio]').forEach(el => {
    const precio = parseFloat(el.dataset.precio);
    const monedaBase = el.dataset.monedaBase;
    const precioDisplay = calcularPrecio(precio, monedaBase, moneda, tasa);
    el.textContent = formatPrice(precioDisplay, moneda);
  });

  // Actualizar precios alternativos
  document.querySelectorAll('.price-alt[data-precio]').forEach(el => {
    const precio = parseFloat(el.dataset.precio);
    const monedaBase = el.dataset.monedaBase;
    const precioAlt = calcularPrecio(precio, monedaBase, monedaAlt, tasa);
    el.textContent = formatPrice(precioAlt, monedaAlt);
  });

  // Actualizar detalle de producto
  const mainPrice = document.querySelector('.price-main[data-precio]');
  if (mainPrice) {
    const precio = parseFloat(mainPrice.dataset.precio);
    const monedaBase = mainPrice.dataset.monedaBase;
    mainPrice.textContent = formatPrice(calcularPrecio(precio, monedaBase, moneda, tasa), moneda);

    const secPrice = document.querySelector('.price-secondary');
    if (secPrice) {
      secPrice.textContent = formatPrice(calcularPrecio(precio, monedaBase, monedaAlt, tasa), monedaAlt);
    }
  }
}
