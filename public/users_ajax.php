<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\UserModel;
use App\Controllers\UserController;

$controller = new UserController();
$controller->ajaxList();
$model = new UserModel();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

$limit = 10;
$offset = ($page - 1) * $limit;

$users = $model->getFilteredSortedUsers($query, $limit, $offset, $sort, $order);
$totalUsers = $model->getFilteredUserCount($query);
$totalPages = ceil($totalUsers / $limit);

// 🔁 Alternar orden para el próximo clic
$nextOrder = ($order === 'ASC') ? 'desc' : 'asc';
?>

<!-- 📤 Botón de exportación -->
<div class="text-end mb-2">
  <a href="export_excel.php?query=<?= urlencode($query) ?>&format=xlsx" class="btn btn-outline-success btn-sm me-2" data-bs-toggle="tooltip" title="Exportar a Excel">📤 Excel</a>
  <a href="export_excel.php?query=<?= urlencode($query) ?>&format=csv" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="tooltip" title="Exportar a CSV">📄 CSV</a>
  <a href="export_excel.php?query=<?= urlencode($query) ?>&format=txt" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Exportar a TXT">📝 TXT</a>
</div>

<table class="table table-bordered table-hover">
  <thead class="table-primary">
    <tr>
      <th><a href="#" class="sort-link" data-sort="id" data-order="<?= $nextOrder ?>">ID</a></th>
      <th><a href="#" class="sort-link" data-sort="name" data-order="<?= $nextOrder ?>">Nombre</a></th>
      <th><a href="#" class="sort-link" data-sort="email" data-order="<?= $nextOrder ?>">Email</a></th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
      <form method="POST" class="form-box">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <td><?= $user['id'] ?></td>
        <td><input type="text" name="name" value="<?= $user['name'] ?>" class="form-control" required></td>
        <td><input type="email" name="email" value="<?= $user['email'] ?>" class="form-control" required></td>
        <td class="d-flex gap-2">
          <button type="submit" class="btn btn-success btn-sm">Actualizar</button>
          <a href="index.php?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
        </td>
      </form>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- 🔢 Paginación AJAX -->
<nav>
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
        <a class="page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>