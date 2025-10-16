<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>A To B • Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }

    /* Sidebar */
    .sidebar { position:fixed; top:0; left:0; width:240px; height:100vh; background:#0d6efd; color:#fff;
               display:flex; flex-direction:column; align-items:center; padding-top:1rem; z-index:1000; }
    .sidebar h5 { color:white; margin-bottom:10px; font-weight:600; }
    .sidebar a { width:90%; margin:5px auto; padding:10px; border-radius:8px; color:#ffffffcc; text-decoration:none;
                 display:flex; align-items:center; gap:10px; transition:all 0.2s ease-in-out; }
    .sidebar a i { min-width:20px; text-align:center; }
    .sidebar a:hover, .sidebar a.active { background:#ffffff33; color:#fff; }

    /* Main */
    main { margin-left:250px; padding:20px; }

    /* Cards & tables */
    .card { border:none; border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.05); }
    .navbar { border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.05); }
    .badge { font-size:0.85rem; }

    /* Charts */
    .chart-container { position:relative; height:280px; width:100%; margin:0 auto; }
    canvas { display:block; width:100% !important; height:100% !important; }
    /* Make DataTables export buttons visible without hover */
    .dt-buttons .btn-outline-secondary {
      color: #495057 !important;          /* Dark gray text (Bootstrap body color) */
      background-color: transparent !important;
      border-color: #6c757d !important;
    }

    /* Remove hover/focus/active effects */
    .dt-buttons .btn-outline-secondary:hover,
    .dt-buttons .btn-outline-secondary:focus,
    .dt-buttons .btn-outline-secondary:active,
    .dt-buttons .btn-outline-secondary.active {
      color: #495057 !important;          /* Keep text visible on hover */
      background-color: transparent !important;
      border-color: #6c757d !important;
      box-shadow: none !important;
    }
    /* Make the search box visually consistent with toolbar */
    .dataTables_filter {
      display: flex !important;
      justify-content: flex-end;
      align-items: center;
      gap: 0.5rem;
    }
    .dataTables_filter input {
      width: 200px !important;
    }

    /* Responsive */
    @media (max-width:768px) {
      .sidebar { position:relative; width:100%; height:auto; flex-direction:row; justify-content:space-around; padding:10px 0; }
      .sidebar a { margin:0; padding:8px 10px; justify-content:center; }
      main { margin-left:0; padding:10px; }
    }

  </style>
</head>
<body>
