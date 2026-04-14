// ┌────────────────────────────────────────────┐
// │ Módulo: TablaUsuarios                      │
// └────────────────────────────────────────────┘
function inicializarTablaUsuarios() {
  $('#usuariosTable').DataTable({
    ajax: {
      url: '/crud-mvc-php/public/usuarios/ajax',
      type: 'POST'
    },
    serverSide: true,
    processing: true,
    responsive: true,
    columns: [
      { data: 'name' },
      { data: 'email' },
      {
        data: 'rol',
        render: rol => {
          const icons = {
            admin: '<i class="bi bi-shield-lock text-danger"></i> Admin',
            editor: '<i class="bi bi-pencil-square text-warning"></i> Editor',
            viewer: '<i class="bi bi-eye text-info"></i> Viewer'
          };
          return icons[rol] || rol;
        }
      },
      {
        data: 'estado',
        render: estado => estado === 'activo'
          ? '<span class="badge bg-success">Activo</span>'
          : '<span class="badge bg-secondary">Inactivo</span>'
      },
      {
        data: null,
        orderable: false,
        render: data => `
          <button class="btn btn-sm btn-primary me-1 edit-btn"
            data-id="${data.id}"
            data-name="${data.name}"
            data-email="${data.email}"
            data-rol="${data.rol}"
            data-estado="${data.estado}">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick="confirmDelete(${data.id})">
            <i class="bi bi-trash"></i>
          </button>
        `
      }
    ]
  });
}

// ┌────────────────────────────────────────────┐
// │ Módulo: ModalEditor                        │
// └────────────────────────────────────────────┘
function inicializarEditorUsuario() {
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.edit-btn');
    if (!btn) return;

    document.getElementById('editId').value = btn.dataset.id;
    document.getElementById('editName').value = btn.dataset.name;
    document.getElementById('editEmail').value = btn.dataset.email;
    document.getElementById('editRol').value = btn.dataset.rol;
    document.getElementById('editEstado').value = btn.dataset.estado;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
  });

  const updateBtn = document.getElementById('updateBtn');
  updateBtn.addEventListener('click', async (event) => {
    event.preventDefault();

    const data = {
      id: document.getElementById('editId').value.trim(),
      name: document.getElementById('editName').value.trim(),
      email: document.getElementById('editEmail').value.trim(),
      rol: document.getElementById('editRol').value,
      estado: document.getElementById('editEstado').value
    };

    if (!data.name || !data.email) {
      mostrarToast('⚠️ Nombre y correo son obligatorios', 'warning');
      return;
    }

    try {
      const response = await fetch('/crud-mvc-php/public/usuarios/actualizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await response.json();
      console.log('Respuesta del servidor:', result);

      if (result.success) {
        mostrarToast('✅ Usuario actualizado correctamente', 'success');
        $('#usuariosTable').DataTable().ajax.reload();
        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
      } else {
        mostrarToast(`❌ Error: ${result.message}`, 'danger');
      }
    } catch (error) {
      console.error('Error de conexión:', error);
      mostrarToast('❌ Error al conectar con el servidor', 'danger');
    }
  });
}

// ┌────────────────────────────────────────────┐
// │ Módulo: AccionesUsuario                    │
// └────────────────────────────────────────────┘
function confirmDelete(id) {
  if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) return;

  fetch(`/crud-mvc-php/public/usuarios/eliminar/${id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  })
    .then(res => res.json())
    .then(response => {
      if (response.success) {
        mostrarToast('✅ Usuario eliminado correctamente', 'success');
        $('#usuariosTable').DataTable().ajax.reload();
      } else {
        mostrarToast(`❌ Error: ${response.message}`, 'danger');
      }
    })
    .catch(error => {
      console.error('Error al eliminar:', error);
      mostrarToast('❌ Error al conectar con el servidor', 'danger');
    });
}

// ┌────────────────────────────────────────────┐
// │ Módulo: ToastManager                       │
// └────────────────────────────────────────────┘
function mostrarToast(mensaje, tipo = 'info') {
  const iconos = {
    success: '✅',
    danger: '❌',
    warning: '⚠️',
    info: 'ℹ️'
  };

  const toast = document.createElement('div');
  toast.className = `toast-message ${tipo}`;

  const icon = document.createElement('span');
  icon.className = 'toast-icon';
  icon.textContent = iconos[tipo] || 'ℹ️';

  const texto = document.createElement('span');
  texto.textContent = mensaje;

  toast.appendChild(icon);
  toast.appendChild(texto);

  const container = document.getElementById('toastContainer');
  container.appendChild(toast);

  setTimeout(() => toast.remove(), 4000);
}

// ┌────────────────────────────────────────────┐
// │ Inicialización global                      │
// └────────────────────────────────────────────┘
document.addEventListener('DOMContentLoaded', () => {
  inicializarTablaUsuarios();
  inicializarEditorUsuario(); // Activación inicial

  // ✅ Reaplicar listener después de cada carga dinámica
  document.addEventListener('usuariosCargados', () => {
    inicializarEditorUsuario();
  });
});