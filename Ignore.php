<a href="<?= ROOT ?>courses" class="nav-link <?= (isset($data['active']) && $data['active']=='courses') ? 'active' : '' ?>">Volver al listado de cursos</a>

<a href="<?= ROOT . (!empty($data['back']) ? $data['back'] : 'shop') ?>" class="btn btn-success">Volver al listado de productos</a>
