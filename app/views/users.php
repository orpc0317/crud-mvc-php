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
        <form method="POST" action="/crud-mvc-php/public/usuarios/crear">
          <input type="text" name="name" class="form-control mb-2" placeholder="Nombre" required>
          <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
          <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select name="rol" id="rol" class="form-select" required>
              <option value="">Seleccione un rol</option>
              <option value="admin">Administrador</option>
              <option value="editor">Editor</option>
              <option value="viewer">Visualizador</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select" required>
              <option value="">Seleccione estado</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100">Crear</button>
        </form>
      </div>

      <div class="col-md-8">
        <h4>Lista de usuarios</h4>
        <table id="usuariosTable" class="table table-striped table-hover w-100">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- ✅ Contenedor de toasts -->
  <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>

  <!-- ✅ Modal de edición -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editId">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="editName" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="editName" required>
            </div>
            <div class="col-md-6">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" required>
            </div>
            <div class="col-md-6">
              <label for="editRol" class="form-label">Rol</label>
              <select class="form-select" id="editRol">
                <option value="admin">Administrador</option>
                <option value="editor">Editor</option>
                <option value="viewer">Visualizador</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="editEstado" class="form-label">Estado</label>
              <select class="form-select" id="editEstado">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="updateBtn">Actualizar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="/crud-mvc-php/public/assets/js/usuarios.js" defer></script>  
</body>

</html>