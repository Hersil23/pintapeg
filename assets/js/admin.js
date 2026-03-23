/**
 * PintaPeg Admin - Script principal del dashboard
 */

const API = '/api';
let csrfToken = '';
let currentUser = null;

// =============================================
// Inicializacion
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  checkAuth();
});

// =============================================
// Autenticacion
// =============================================
async function checkAuth() {
  try {
    const res = await fetch(`${API}/auth.php?action=check`);
    const data = await res.json();

    if (!data.authenticated) {
      // Si no estamos en login, redirigir
      if (!window.location.pathname.includes('admin/index.html')) {
        window.location.href = '/admin/index.html';
      }
      return;
    }

    csrfToken = data.csrf_token;
    currentUser = data.user;

    // Si estamos en login, redirigir al dashboard
    if (window.location.pathname.includes('admin/index.html')) {
      window.location.href = '/admin/dashboard.html';
      return;
    }

    // Actualizar UI de usuario
    const userEl = document.getElementById('user-name');
    if (userEl) userEl.textContent = currentUser.nombre;

    // Inicializar pagina
    initPage();
  } catch (err) {
    if (!window.location.pathname.includes('admin/index.html')) {
      window.location.href = '/admin/index.html';
    }
  }
}

// =============================================
// Login
// =============================================
async function handleLogin(e) {
  e.preventDefault();
  const form = e.target;
  const errorEl = document.getElementById('login-error');
  const btn = form.querySelector('button');

  const email = form.email.value.trim();
  const password = form.password.value;

  if (!email || !password) {
    showLoginError('Completa todos los campos');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Ingresando...';

  try {
    const res = await fetch(`${API}/auth.php?action=login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }),
    });

    const data = await res.json();

    if (data.success) {
      window.location.href = '/admin/dashboard.html';
    } else {
      showLoginError(data.error || 'Error al iniciar sesion');
    }
  } catch (err) {
    showLoginError('Error de conexion');
  }

  btn.disabled = false;
  btn.textContent = 'Ingresar';
}

function showLoginError(msg) {
  const el = document.getElementById('login-error');
  if (el) {
    el.textContent = msg;
    el.style.display = 'block';
  }
}

// =============================================
// Logout
// =============================================
async function handleLogout() {
  try {
    await fetch(`${API}/auth.php?action=logout`, { method: 'POST' });
  } catch (e) { /* ignore */ }
  window.location.href = '/admin/index.html';
}

// =============================================
// API Helper
// =============================================
async function apiCall(endpoint, options = {}) {
  const defaults = {
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken,
    },
  };

  const config = { ...defaults, ...options };
  if (options.headers) {
    config.headers = { ...defaults.headers, ...options.headers };
  }

  const res = await fetch(`${API}/${endpoint}`, config);
  const data = await res.json();

  if (res.status === 401) {
    window.location.href = '/admin/index.html';
    return null;
  }

  return data;
}

// =============================================
// Upload de imagen
// =============================================
async function uploadImage(fileInput) {
  const file = fileInput.files[0];
  if (!file) return null;

  const formData = new FormData();
  formData.append('imagen', file);

  const res = await fetch(`${API}/upload.php`, {
    method: 'POST',
    headers: { 'X-CSRF-Token': csrfToken },
    body: formData,
  });

  const data = await res.json();

  if (data.success) {
    return data.archivo;
  }

  throw new Error(data.error || 'Error al subir imagen');
}

// =============================================
// Toast notifications
// =============================================
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// =============================================
// Modal helpers
// =============================================
function openModal(id) {
  document.getElementById(id).classList.add('active');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

// =============================================
// Inicializacion por pagina
// =============================================
function initPage() {
  const path = window.location.pathname;

  if (path.includes('dashboard.html')) {
    loadDashboard();
  } else if (path.includes('categorias.html')) {
    loadCategorias();
  } else if (path.includes('productos.html')) {
    loadProductos();
  } else if (path.includes('ventas.html')) {
    loadVentas();
  } else if (path.includes('config.html')) {
    loadConfig();
  }
}

// =============================================
// DASHBOARD
// =============================================
async function loadDashboard() {
  const stats = await apiCall('ventas.php?action=stats');
  if (!stats) return;

  document.getElementById('stat-ventas-hoy').textContent = stats.hoy.ventas;
  document.getElementById('stat-monto-hoy').textContent = '$' + stats.hoy.monto_usd.toFixed(2);
  document.getElementById('stat-ventas-mes').textContent = stats.mes.ventas;
  document.getElementById('stat-monto-mes').textContent = '$' + stats.mes.monto_usd.toFixed(2);
  document.getElementById('stat-ventas-total').textContent = stats.total.ventas;
  document.getElementById('stat-monto-total').textContent = '$' + stats.total.monto_usd.toFixed(2);

  // Cargar tasa actual
  const tasa = await apiCall('tasa.php');
  if (tasa) {
    document.getElementById('stat-tasa').textContent =
      'Bs. ' + (tasa.valor_activo || 0).toFixed(2) + ' (' + tasa.tasa_activa + ')';
  }
}

// =============================================
// CATEGORIAS
// =============================================
let categorias = [];

async function loadCategorias() {
  const data = await apiCall('categorias.php?all=1');
  if (!data) return;

  categorias = data;
  renderCategorias();
}

function renderCategorias() {
  const tbody = document.getElementById('categorias-tbody');
  if (!tbody) return;

  tbody.innerHTML = categorias.map(cat => `
    <tr>
      <td>${cat.id}</td>
      <td>${cat.imagen ? `<img src="/uploads/productos/${cat.imagen}" alt="${cat.nombre}">` : '-'}</td>
      <td><strong>${cat.nombre}</strong><br><small style="color:var(--admin-text-muted)">${cat.slug}</small></td>
      <td>${cat.orden}</td>
      <td><span class="badge ${cat.activo ? 'badge-success' : 'badge-danger'}">${cat.activo ? 'Activa' : 'Inactiva'}</span></td>
      <td>
        <button class="btn btn-secondary btn-sm" onclick="editCategoria(${cat.id})">Editar</button>
        <button class="btn btn-danger btn-sm" onclick="deleteCategoria(${cat.id}, '${cat.nombre}')">Eliminar</button>
      </td>
    </tr>
  `).join('');
}

function openCategoriaModal(cat = null) {
  const form = document.getElementById('categoria-form');
  form.reset();
  document.getElementById('categoria-id').value = cat ? cat.id : '';
  document.getElementById('modal-categoria-title').textContent = cat ? 'Editar Categoria' : 'Nueva Categoria';

  if (cat) {
    document.getElementById('cat-nombre').value = cat.nombre;
    document.getElementById('cat-orden').value = cat.orden;
    document.getElementById('cat-activo').checked = cat.activo == 1;
  }

  openModal('modal-categoria');
}

function editCategoria(id) {
  const cat = categorias.find(c => c.id === id);
  if (cat) openCategoriaModal(cat);
}

async function saveCategoria(e) {
  e.preventDefault();

  const id = document.getElementById('categoria-id').value;
  const nombre = document.getElementById('cat-nombre').value.trim();
  const orden = parseInt(document.getElementById('cat-orden').value) || 0;
  const activo = document.getElementById('cat-activo').checked ? 1 : 0;

  if (!nombre) {
    showToast('El nombre es requerido', 'error');
    return;
  }

  const body = { nombre, orden, activo };

  // Subir imagen si hay
  const fileInput = document.getElementById('cat-imagen');
  if (fileInput.files.length > 0) {
    try {
      body.imagen = await uploadImage(fileInput);
    } catch (err) {
      showToast(err.message, 'error');
      return;
    }
  }

  let endpoint = 'categorias.php';
  if (id) {
    endpoint += '?action=update';
    body.id = parseInt(id);
  }

  const data = await apiCall(endpoint, {
    method: 'POST',
    body: JSON.stringify(body),
  });

  if (data && (data.success || data.id)) {
    showToast(id ? 'Categoria actualizada' : 'Categoria creada');
    closeModal('modal-categoria');
    loadCategorias();
  } else {
    showToast(data?.error || 'Error al guardar', 'error');
  }
}

async function deleteCategoria(id, nombre) {
  if (!confirm(`Eliminar la categoria "${nombre}"?`)) return;

  const data = await apiCall('categorias.php?action=delete', {
    method: 'POST',
    body: JSON.stringify({ id }),
  });

  if (data && data.success) {
    showToast('Categoria eliminada');
    loadCategorias();
  } else {
    showToast(data?.error || 'Error al eliminar', 'error');
  }
}

// =============================================
// PRODUCTOS
// =============================================
let productos = [];

async function loadProductos() {
  // Cargar categorias para el select
  const cats = await apiCall('categorias.php?all=1');
  if (cats) {
    categorias = cats;
    const select = document.getElementById('prod-categoria');
    if (select) {
      select.innerHTML = '<option value="">Seleccionar...</option>' +
        cats.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
    }
  }

  const data = await apiCall('productos.php?all=1');
  if (!data) return;

  productos = data;
  renderProductos();
}

function renderProductos() {
  const tbody = document.getElementById('productos-tbody');
  if (!tbody) return;

  tbody.innerHTML = productos.map(prod => `
    <tr>
      <td>${prod.id}</td>
      <td>${prod.imagen ? `<img src="/uploads/productos/${prod.imagen}" alt="${prod.nombre}">` : '-'}</td>
      <td>
        <strong>${prod.nombre}</strong><br>
        <small style="color:var(--admin-text-muted)">${prod.categoria_nombre || '-'}</small>
      </td>
      <td>$${parseFloat(prod.precio).toFixed(2)} <small>${prod.moneda_base.toUpperCase()}</small></td>
      <td>${prod.stock}</td>
      <td>
        <span class="badge ${prod.activo ? 'badge-success' : 'badge-danger'}">${prod.activo ? 'Activo' : 'Inactivo'}</span>
        ${prod.destacado ? '<span class="badge badge-warning">Destacado</span>' : ''}
      </td>
      <td>
        <button class="btn btn-secondary btn-sm" onclick="editProducto(${prod.id})">Editar</button>
        <button class="btn btn-danger btn-sm" onclick="deleteProducto(${prod.id}, '${prod.nombre.replace(/'/g, "\\'")}')">Eliminar</button>
      </td>
    </tr>
  `).join('');
}

function openProductoModal(prod = null) {
  const form = document.getElementById('producto-form');
  form.reset();
  document.getElementById('producto-id').value = prod ? prod.id : '';
  document.getElementById('modal-producto-title').textContent = prod ? 'Editar Producto' : 'Nuevo Producto';

  if (prod) {
    document.getElementById('prod-nombre').value = prod.nombre;
    document.getElementById('prod-categoria').value = prod.categoria_id;
    document.getElementById('prod-descripcion').value = prod.descripcion || '';
    document.getElementById('prod-precio').value = prod.precio;
    document.getElementById('prod-moneda').value = prod.moneda_base;
    document.getElementById('prod-stock').value = prod.stock;
    document.getElementById('prod-activo').checked = prod.activo == 1;
    document.getElementById('prod-destacado').checked = prod.destacado == 1;
  }

  openModal('modal-producto');
}

function editProducto(id) {
  const prod = productos.find(p => p.id === id);
  if (prod) openProductoModal(prod);
}

async function saveProducto(e) {
  e.preventDefault();

  const id = document.getElementById('producto-id').value;
  const body = {
    nombre: document.getElementById('prod-nombre').value.trim(),
    categoria_id: parseInt(document.getElementById('prod-categoria').value),
    descripcion: document.getElementById('prod-descripcion').value.trim(),
    precio: parseFloat(document.getElementById('prod-precio').value),
    moneda_base: document.getElementById('prod-moneda').value,
    stock: parseInt(document.getElementById('prod-stock').value) || 0,
    activo: document.getElementById('prod-activo').checked ? 1 : 0,
    destacado: document.getElementById('prod-destacado').checked ? 1 : 0,
  };

  if (!body.nombre || !body.categoria_id || !body.precio) {
    showToast('Nombre, categoria y precio son requeridos', 'error');
    return;
  }

  // Subir imagen si hay
  const fileInput = document.getElementById('prod-imagen');
  if (fileInput.files.length > 0) {
    try {
      body.imagen = await uploadImage(fileInput);
    } catch (err) {
      showToast(err.message, 'error');
      return;
    }
  }

  let endpoint = 'productos.php';
  if (id) {
    endpoint += '?action=update';
    body.id = parseInt(id);
  }

  const data = await apiCall(endpoint, {
    method: 'POST',
    body: JSON.stringify(body),
  });

  if (data && (data.success || data.id)) {
    showToast(id ? 'Producto actualizado' : 'Producto creado');
    closeModal('modal-producto');
    loadProductos();
  } else {
    showToast(data?.error || 'Error al guardar', 'error');
  }
}

async function deleteProducto(id, nombre) {
  if (!confirm(`Eliminar el producto "${nombre}"?`)) return;

  const data = await apiCall('productos.php?action=delete', {
    method: 'POST',
    body: JSON.stringify({ id }),
  });

  if (data && data.success) {
    showToast('Producto eliminado');
    loadProductos();
  } else {
    showToast(data?.error || 'Error al eliminar', 'error');
  }
}

// =============================================
// VENTAS
// =============================================
async function loadVentas() {
  const stats = await apiCall('ventas.php?action=stats');
  if (stats) {
    document.getElementById('stat-ventas-hoy').textContent = stats.hoy.ventas;
    document.getElementById('stat-monto-hoy').textContent = '$' + stats.hoy.monto_usd.toFixed(2);
    document.getElementById('stat-ventas-mes').textContent = stats.mes.ventas;
    document.getElementById('stat-monto-total').textContent = '$' + stats.total.monto_usd.toFixed(2);
  }

  await filtrarVentas();
}

async function filtrarVentas() {
  const desde = document.getElementById('filtro-desde')?.value || '';
  const hasta = document.getElementById('filtro-hasta')?.value || '';

  let endpoint = 'ventas.php?limit=50';
  if (desde) endpoint += `&desde=${desde}`;
  if (hasta) endpoint += `&hasta=${hasta}`;

  const data = await apiCall(endpoint);
  if (!data) return;

  renderVentas(data.ventas || []);
}

function renderVentas(ventas) {
  const tbody = document.getElementById('ventas-tbody');
  if (!tbody) return;

  tbody.innerHTML = ventas.map(v => `
    <tr>
      <td><strong>${v.referencia}</strong></td>
      <td>${v.nombre_cliente}</td>
      <td>$${parseFloat(v.total_usd).toFixed(2)}</td>
      <td>Bs. ${parseFloat(v.total_ves).toFixed(2)}</td>
      <td>${new Date(v.fecha).toLocaleDateString('es-VE')}</td>
      <td>
        <button class="btn btn-secondary btn-sm" onclick="verVenta(${v.id})">Ver</button>
      </td>
    </tr>
  `).join('');

  if (ventas.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--admin-text-muted)">No hay ventas registradas</td></tr>';
  }
}

async function verVenta(id) {
  const data = await apiCall(`ventas.php?id=${id}`);
  if (!data) return;

  const prods = data.productos || [];
  const detalle = `
    <strong>Referencia:</strong> ${data.referencia}<br>
    <strong>Cliente:</strong> ${data.nombre_cliente}<br>
    <strong>Direccion:</strong> ${data.direccion}<br>
    ${data.referencia_entrega ? `<strong>Ref. Entrega:</strong> ${data.referencia_entrega}<br>` : ''}
    <strong>Fecha:</strong> ${new Date(data.fecha).toLocaleString('es-VE')}<br>
    <strong>Tasa:</strong> Bs. ${parseFloat(data.tasa_usada).toFixed(2)}<br><br>
    <strong>Productos:</strong><br>
    ${prods.map(p => `- ${p.nombre} x${p.cantidad} — $${parseFloat(p.subtotal).toFixed(2)}`).join('<br>')}
    <br><br>
    <strong>Total:</strong> $${parseFloat(data.total_usd).toFixed(2)} / Bs. ${parseFloat(data.total_ves).toFixed(2)}
  `;

  document.getElementById('venta-detalle').innerHTML = detalle;
  openModal('modal-venta');
}

// =============================================
// CONFIG
// =============================================
async function loadConfig() {
  const config = await apiCall('config-tienda.php');
  if (!config) return;

  document.getElementById('cfg-tasa-activa').value = config.tasa_activa || 'bcv';
  document.getElementById('cfg-moneda-default').value = config.moneda_default || 'usd';
  document.getElementById('cfg-whatsapp').value = config.whatsapp || '';

  // Cargar tasas actuales
  const tasa = await apiCall('tasa.php');
  if (tasa && tasa.tasas) {
    const bcv = tasa.tasas.bcv;
    const paralelo = tasa.tasas.paralelo;
    document.getElementById('tasa-bcv').textContent = bcv ? `Bs. ${bcv.valor.toFixed(2)} (${bcv.fecha})` : 'Sin datos';
    document.getElementById('tasa-paralelo').textContent = paralelo ? `Bs. ${paralelo.valor.toFixed(2)} (${paralelo.fecha})` : 'Sin datos';
  }
}

async function saveConfig(e) {
  e.preventDefault();

  const body = {
    tasa_activa: document.getElementById('cfg-tasa-activa').value,
    moneda_default: document.getElementById('cfg-moneda-default').value,
    whatsapp: document.getElementById('cfg-whatsapp').value.trim(),
  };

  const data = await apiCall('config-tienda.php', {
    method: 'POST',
    body: JSON.stringify(body),
  });

  if (data && data.success) {
    showToast('Configuracion guardada');
  } else {
    showToast(data?.error || 'Error al guardar', 'error');
  }
}

async function actualizarTasas() {
  const data = await apiCall('tasa.php?action=update', { method: 'POST' });

  if (data && data.success) {
    showToast('Tasas actualizadas');
    loadConfig();
  } else {
    showToast(data?.error || 'Error al actualizar tasas', 'error');
  }
}

// =============================================
// Sidebar toggle (mobile)
// =============================================
function toggleSidebar() {
  document.querySelector('.admin-sidebar')?.classList.toggle('open');
}
