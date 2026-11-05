<?php $b = $base ?? ''; $cu = current_user() ?? []; $nm = trim((string)($cu['name'] ?? '')); $ini = ''; foreach (array_slice(preg_split('/\s+/', $nm) ?: [], 0, 2) as $w) { if ($w !== '') $ini .= strtoupper(substr($w, 0, 1)); } if ($ini === '') $ini = 'U'; ?>
<nav class="sidebar d-flex flex-column p-3">
  <a class="brand text-white text-decoration-none fs-5 fw-bold mb-4" href="<?= $b ?>dashboard.php"><span class="brand-mark"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V10" stroke="#fff" stroke-width="3.4" stroke-linecap="round"/><path d="M10.5 20V4" stroke="#fff" stroke-width="3.4" stroke-linecap="round" opacity=".85"/><path d="M17 20v-8" stroke="#fff" stroke-width="3.4" stroke-linecap="round" opacity=".65"/><path d="M3 5.5 9 3l5 3 6-4" stroke="#bae6fd" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="brand-text">CRM Pro<small>Sales Intelligence</small></span></a>
  <a class="nav-link <?= ($active??'')==='dashboard'?'active':'' ?>" href="<?= $b ?>dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a class="nav-link <?= ($active??'')==='customers'?'active':'' ?>" href="<?= $b ?>customers.php"><i class="bi bi-people"></i> Customers</a>
  <a class="nav-link <?= ($active??'')==='leads'?'active':'' ?>" href="<?= $b ?>leads.php"><i class="bi bi-funnel"></i> Leads</a>
  <a class="nav-link <?= ($active??'')==='deals'?'active':'' ?>" href="<?= $b ?>deals.php"><i class="bi bi-kanban"></i> Pipeline</a>
  <a class="nav-link <?= ($active??'')==='tasks'?'active':'' ?>" href="<?= $b ?>tasks.php"><i class="bi bi-check2-square"></i> Tasks</a>
  <a class="nav-link <?= ($active??'')==='employees'?'active':'' ?>" href="<?= $b ?>employees.php"><i class="bi bi-person-badge"></i> Employees</a>
  <a class="nav-link <?= ($active??'')==='reports'?'active':'' ?>" href="<?= $b ?>reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
  <a class="nav-link <?= ($active??'')==='subscription'?'active':'' ?>" href="<?= $b ?>subscription.php"><i class="bi bi-credit-card"></i> Subscription</a>
  <a class="nav-link <?= ($active??'')==='settings'?'active':'' ?>" href="<?= $b ?>settings.php"><i class="bi bi-gear"></i> Settings</a>
  <div class="mt-auto"><div class="sidebar-user"><span class="avatar-initials"><?= e($ini) ?></span><span class="who"><span class="nm"><?= e(current_user()['name'] ?? '') ?></span><br><span class="rl"><?= e(current_user()['role'] ?? '') ?></span></span></div></div>
  <a class="nav-link" href="<?= $b ?>auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>
