<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Loan Management System'; ?></title>
    <?php $builtCss = function_exists('getBuiltCssPath') ? getBuiltCssPath() : null; ?>
    <?php if ($builtCss): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($builtCss); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="/bbloan/src/assets/css/style.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    $alert = getAlert();
    if ($alert): ?>
        <div class="alert alert-<?php echo $alert['type']; ?>" id="alert">
            <span><?php echo htmlspecialchars($alert['message']); ?></span>
            <button onclick="closeAlert()" class="alert-close">&times;</button>
        </div>
    <?php endif; ?>
</body>