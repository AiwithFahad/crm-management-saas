<?php $b='../'; $cu = function_exists('current_user') ? (current_user() ?? []) : []; $nm = trim((string)($cu['name'] ?? '')); $ini = ''; foreach (array_slice(preg_split('/\s+/', $nm) ?: [], 0, 2) as $w) { if ($w !== '') $ini .= strtoupper(substr($w, 0, 1)); } if ($ini === '') $ini = 'U'; ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($pageTitle ?? '') ?> | Super Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"><link href="../assets/css/style.css" rel="stylesheet">
<script>(function(){try{if(localStorage.getItem('crm-theme')==='dark')document.documentElement.setAttribute('data-theme','dark');}catch(e){}})();</script></head>
<body><div class="layout"><?php include __DIR__ . '/sidebar.php'; ?><main class="main p-4">
<header class="topbar"><button class="icon-btn" id="sidebarToggle" type="button" title="Collapse sidebar" aria-label="Collapse sidebar"><i class="bi bi-layout-sidebar-inset"></i></button><h1><?= e($pageTitle ?? '') ?></h1>
<div class="search-wrap"><i class="bi bi-search"></i><input class="form-control" id="globalSearch" type="search" placeholder="Search navigation…" aria-label="Search navigation"></div>
<span class="ms-auto"></span>
<button class="icon-btn" type="button" title="Notifications" aria-label="Notifications"><i class="bi bi-bell"></i><span class="dot"></span></button>
<button class="icon-btn" id="themeToggle" type="button" title="Toggle theme" aria-label="Toggle theme"><i class="bi bi-moon"></i></button>
<span class="avatar-initials" title="<?= e($nm) ?>"><?= e($ini) ?></span></header>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?><?php if ($m = flash('error')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
