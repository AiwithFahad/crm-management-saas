<?php $b = $base ?? ''; ?>
<nav class="sidebar d-flex flex-column p-3">
  <a class="brand text-white text-decoration-none fs-5 fw-bold mb-4" href="<?= $b ?>dashboard.php"><i class="bi bi-kanban"></i> CRM Pro</a>
  <a class="nav-link <?= ($active??'')==='dashboard'?'active':'' ?>" href="<?= $b ?>dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a class="nav-link <?= ($active??'')==='customers'?'active':'' ?>" href="<?= $b ?>customers.php"><i class="bi bi-people"></i> Customers</a>
  <a class="nav-link <?= ($active??'')==='leads'?'active':'' ?>" href="<?= $b ?>leads.php"><i class="bi bi-funnel"></i> Leads</a>
  <a class="nav-link <?= ($active??'')==='deals'?'active':'' ?>" href="<?= $b ?>deals.php"><i class="bi bi-kanban"></i> Pipeline</a>
  <a class="nav-link <?= ($active??'')==='tasks'?'active':'' ?>" href="<?= $b ?>tasks.php"><i class="bi bi-check2-square"></i> Tasks</a>
  <a class="nav-link <?= ($active??'')==='employees'?'active':'' ?>" href="<?= $b ?>employees.php"><i class="bi bi-person-badge"></i> Employees</a>
  <a class="nav-link <?= ($active??'')==='reports'?'active':'' ?>" href="<?= $b ?>reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
  <a class="nav-link <?= ($active??'')==='subscription'?'active':'' ?>" href="<?= $b ?>subscription.php"><i class="bi bi-credit-card"></i> Subscription</a>
  <a class="nav-link <?= ($active??'')==='settings'?'active':'' ?>" href="<?= $b ?>settings.php"><i class="bi bi-gear"></i> Settings</a>
  <div class="mt-auto small text-white-50"><?= e(current_user()['name'] ?? '') ?><br><?= e(current_user()['role'] ?? '') ?></div>
  <a class="nav-link" href="<?= $b ?>auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>
