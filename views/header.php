<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Basic SEO -->
    <meta name="description" content="PenPoint - Your hub for news and discussions.">
    <meta name="keywords" content="news, articles, comments, PenPoint">
    <meta name="author" content="Shamin Memary">

    <!-- Social Sharing (OG & Twitter) -->
    <meta property="og:title" content="PenPoint">
    <meta property="og:description" content="Discover, create, and discuss articles with a vibrant community.">
    <meta property="og:image" content="/public/images/pen.png">
    <meta property="og:url" content="https://yourdomain.com/">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="PenPoint">
    <meta name="twitter:description" content="Discover, create, and discuss articles with a vibrant community.">
    <meta name="twitter:image" content="../public/images/pen.png">

    <!-- Title (Dynamic) -->
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | PenPoint' : 'PenPoint'; ?></title>
    

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= image('pen.png') ?>">

    <!-- CSS (Tailwind or other) -->
    <link href="/dist/output.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
      rel="stylesheet"
    >

    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#2C3E50">

    
    <link href="/dist/output.css" rel="stylesheet">
</head>
