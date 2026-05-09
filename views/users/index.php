<?php
$currentSearch = $currentSearch ?? trim($_GET['q'] ?? '');
$currentLetter = $currentLetter ?? strtoupper(trim($_GET['letter'] ?? ''));
$currentRoleFilter = $currentRoleFilter ?? trim($_GET['role_filter'] ?? '');
$currentSortBy = $currentSortBy ?? ($_GET['sort_by'] ?? 'id');
$currentSortDir = $currentSortDir ?? strtoupper($_GET['sort_dir'] ?? 'DESC');

function buildUsersBackofficeUrl(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    $params['route'] = 'backoffice/users';
    unset($params['action']);

    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
        }
    }

    return base_url() . '/index.php?' . http_build_query($params);
}

function userSortLink(string $column, string $currentSortBy, string $currentSortDir): string
{
    $nextDir = ($currentSortBy === $column && $currentSortDir === 'ASC') ? 'DESC' : 'ASC';

    return buildUsersBackofficeUrl(['sort_by' => $column, 'sort_dir' => $nextDir]);
}
?>
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <span class="badge bg-primary-subtle text-primary mb-2">Secondary</span>
            <h4 class="fs-18 fw-semibold m-0">Users Management</h4>
            <p class="text-muted mb-0 mt-1">Manage application users from the same produits backoffice shell.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-1"><?= $editingUser ? 'Edit User #' . (int) $editingUser['id'] : 'Add User' ?></h5>
                    <p class="text-muted mb-4">Create user accounts and assign their role.</p>

                    <form method="post" action="<?= htmlspecialchars(action_url('save_user'), ENT_QUOTES, 'UTF-8') ?>" class="form-shell">
                        <input type="hidden" name="id" value="<?= $editingUser ? (int) $editingUser['id'] : '' ?>">
                        <div class="field">
                            <label for="admin_fullname">Full Name</label>
                            <input id="admin_fullname" type="text" name="fullname" value="<?= htmlspecialchars((string) ($editingUser['fullname'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="field">
                            <label for="admin_email">Email</label>
                            <input id="admin_email" type="text" name="email" value="<?= htmlspecialchars((string) ($editingUser['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="field">
                            <label for="admin_password"><?= $editingUser ? 'New Password (optional)' : 'Password' ?></label>
                            <input id="admin_password" type="password" name="password">
                        </div>
                        <div class="field">
                            <label for="admin_secret_code"><?= $editingUser ? 'New Secret Code (optional)' : 'Secret Code' ?></label>
                            <input id="admin_secret_code" type="password" name="secret_code">
                        </div>
                        <div class="field">
                            <label for="admin_role">Role</label>
                            <select id="admin_role" name="role">
                                <option value="user" <?= (($editingUser['role'] ?? 'user') === 'user') ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= (($editingUser['role'] ?? 'user') === 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="actions">
                            <button class="btn btn-primary" type="submit" name="submit" value="SaveUser"><?= $editingUser ? 'Save User' : 'Add User' ?></button>
                            <?php if ($editingUser): ?>
                                <a class="btn btn-secondary" href="<?= htmlspecialchars(route_url('backoffice/users'), ENT_QUOTES, 'UTF-8') ?>">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="header-line mb-3">
                        <div>
                            <h5 class="card-title mb-1">Users</h5>
                            <p class="text-muted mb-0">Search, filter, and update roles.</p>
                        </div>
                    </div>

                    <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="form-shell mb-3">
                        <input type="hidden" name="route" value="backoffice/users">
                        <div class="row">
                            <div class="col-6">
                                <div class="field">
                                    <label for="search_admin">Search</label>
                                    <input id="search_admin" type="text" name="q" placeholder="Name or email" value="<?= htmlspecialchars((string) $currentSearch, ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="field">
                                    <label for="role_filter_admin">Role</label>
                                    <select id="role_filter_admin" name="role_filter">
                                        <option value="" <?= $currentRoleFilter === '' ? 'selected' : '' ?>>All roles</option>
                                        <option value="admin" <?= $currentRoleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="user" <?= $currentRoleFilter === 'user' ? 'selected' : '' ?>>User</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" type="submit">Search</button>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="<?= htmlspecialchars(route_url('backoffice/users'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                        </div>
                    </form>

                    <div class="mb-3">
                        <strong class="me-2">A-Z:</strong>
                        <a href="<?= htmlspecialchars(buildUsersBackofficeUrl(['letter' => '']), ENT_QUOTES, 'UTF-8') ?>">All</a>
                        <?php foreach (range('A', 'Z') as $alpha): ?>
                            <span class="mx-1">
                                <?php if ($currentLetter === $alpha): ?>
                                    <strong><?= $alpha ?></strong>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars(buildUsersBackofficeUrl(['letter' => $alpha]), ENT_QUOTES, 'UTF-8') ?>"><?= $alpha ?></a>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><a href="<?= htmlspecialchars(userSortLink('id', (string) $currentSortBy, (string) $currentSortDir), ENT_QUOTES, 'UTF-8') ?>">ID</a></th>
                                    <th><a href="<?= htmlspecialchars(userSortLink('fullname', (string) $currentSortBy, (string) $currentSortDir), ENT_QUOTES, 'UTF-8') ?>">Name</a></th>
                                    <th><a href="<?= htmlspecialchars(userSortLink('email', (string) $currentSortBy, (string) $currentSortDir), ENT_QUOTES, 'UTF-8') ?>">Email</a></th>
                                    <th><a href="<?= htmlspecialchars(userSortLink('role', (string) $currentSortBy, (string) $currentSortDir), ENT_QUOTES, 'UTF-8') ?>">Role</a></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= (int) $row['id'] ?></td>
                                        <td><?= htmlspecialchars((string) $row['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) $row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <form method="post" action="<?= htmlspecialchars(action_url('change_role'), ENT_QUOTES, 'UTF-8') ?>" class="d-flex gap-2 align-items-center">
                                                <input type="hidden" name="user_id" value="<?= (int) $row['id'] ?>">
                                                <select name="role" style="max-width:130px">
                                                    <option value="user" <?= (($row['role'] ?? 'user') === 'user') ? 'selected' : '' ?>>User</option>
                                                    <option value="admin" <?= (($row['role'] ?? 'user') === 'admin') ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                                <button class="btn btn-secondary" type="submit">Change</button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a href="<?= htmlspecialchars(route_url('backoffice/users', ['edit' => (int) $row['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                                <a href="<?= htmlspecialchars(action_url('delete', ['id' => (int) $row['id']]), ENT_QUOTES, 'UTF-8') ?>" onclick="return confirm('Delete this user?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
