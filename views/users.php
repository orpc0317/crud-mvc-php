<?php
$toastMessage = '';
if (isset($_GET['success'])) {
  switch ($_GET['success']) {
    case 'created':
      $toastMessage = 'Usuario creado exitosamente.';
      break;
    case 'updated':
      $toastMessage = 'Usuario actualizado correctamente.';
      break;
    case 'deleted':
      $toastMessage = 'Usuario eliminado.';
      break;
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>CRUD MVC PHP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }

    .navbar.dark-mode {
      background-color: #1f1f1f;
    }

    .toast.dark-mode {
      background-color: #333;
      color: #fff;
    }

    .form-control.dark-mode,
    #searchInput.dark-mode {
      background-color: #2c2c2c;
      color: #f1f1f1;
      border: 1px solid #555;
    }

    h4.dark-mode {
      color: #f1f1f1;
    }
  </style>
</head>

<body class="bg-light">

  <!-- 🧭 Barra superior -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">🧭 Dashboard</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Usuarios</a></li>
        </ul>
        <div class="form-check form-switch text-white ms-auto">
          <input class="form-check-input" type="checkbox" id="themeToggle">
          <label class="form-check-label" for="themeToggle">Modo oscuro</label>
        </div>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row mb-4">
      <div class="col-md-4">
        <h4>Agregar usuario</h4>
        <form method="POST" class="form-box">
          <input type="hidden" name="action" value="create">
          <input type="text" name="name" class="form-control mb-2" placeholder="Nombre" required data-bs-toggle="tooltip" title="Nombre completo del usuario">
          <input type="email" name="email" class="form-control mb-2" placeholder="Email" required data-bs-toggle="tooltip" title="Correo electrónico válido">
          <button type="submit" class="btn btn-primary w-100" data-bs-toggle="tooltip" title="Crear nuevo usuario">Crear</button>
        </form>
      </div>

      <div class="col-md-8">
        <h4>Lista de usuarios</h4>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar por nombre o email" data-bs-toggle="tooltip" title="Filtra los usuarios por nombre o correo">
        <div id="userTableContainer"></div>
      </div>
    </div>
  </div>

  <!-- ✅ Toast flotante -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage">Acción completada exitosamente.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
      </div>
    </div>
  </div>

  <!-- ✅ Modal de confirmación -->
  <div class="modal fade" id="confirmCreateModal" tabindex="-1" aria-labelledby="confirmCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="confirmCreateLabel">Confirmar creación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de que deseas crear este usuario?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="confirmCreateBtn">Sí, crear</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 🌗 Modo oscuro
    document.getElementById('themeToggle').addEventListener('change', () => {
      document.body.classList.toggle('dark-mode');
      document.querySelector('.navbar').classList.toggle('dark-mode');
      document.querySelectorAll('.form-control, h4, #searchInput, #liveToast').forEach(el => el.classList.toggle('dark-mode'));
    });

    // 🎯 Toast
    const toastMessage = <?php echo json_encode($toastMessage); ?>;
    if (toastMessage) {
      document.getElementById('toastMessage').textContent = toastMessage;
      new bootstrap.Toast(document.getElementById('liveToast')).show();
    }

    // ✅ Modal de confirmación
    document.querySelectorAll('form.form-box').forEach(form => {
      form.addEventListener('submit', function(event) {
        const action = form.querySelector('input[name="action"]')?.value;
        if (action === 'create') {
          event.preventDefault();
          event.stopPropagation();
          window.pendingCreateForm = form;
          new bootstrap.Modal(document.getElementById('confirmCreateModal')).show();
        }
      });
    });

    document.getElementById('confirmCreateBtn').addEventListener('click', function() {
      if (window.pendingCreateForm) {
        bootstrap.Modal.getInstance(document.getElementById('confirmCreateModal')).hide();
        window.pendingCreateForm.submit();
        window.pendingCreateForm = null;
      }
    });

    // 🔄 AJAX: cargar usuarios
    function loadUsers(page = 1, query = '', sort = 'id', order = 'asc') {
      fetch(`users_ajax.php?page=${page}&query=${encodeURIComponent(query)}&sort=${sort}&order=${order}`)
        .then(response => response.text())
        .then(html => {
          document.getElementById('userTableContainer').innerHTML = html;

          // ✅ Reactivar tooltips después de cargar HTML
          document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
        });
    }

    loadUsers();

    document.getElementById('searchInput').addEventListener('keyup', function() {
      loadUsers(1, this.value);
    });

    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('page-link')) {
        e.preventDefault();
        const page = e.target.dataset.page;
        const query = document.getElementById('searchInput').value;
        loadUsers(page, query);
      }

      if (e.target.classList.contains('sort-link')) {
        e.preventDefault();
        const sort = e.target.dataset.sort;
        const order = e.target.dataset.order;
        const query = document.getElementById('searchInput').value;
        loadUsers(1, query, sort, order);
      }
    });
  </script>
</body>

</html>