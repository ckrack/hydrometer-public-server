<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=getenv('SITE_TITLE')?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Short description of the page (limit to 150 characters) -->
    <!-- In *some* situations this description is used as a part of the snippet shown in the search results. -->
    <meta name="description" content="">

    <link rel="stylesheet" href="/css/c3.min.css">
    <link rel="stylesheet" type="text/css" href="https://v4-alpha.getbootstrap.com/dist/css/bootstrap.min.css">

    <link rel="author" href="humans.txt">

    <script src="/js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="/js/vendor/jquery-3.1.1.min.js"></script>

    <script src="/js/vendor/d3.v3.min.js"></script>
    <script src="/js/vendor/c3.min.js"></script>
</head>
<body>
    <?php $this->insert('partials/nav/nav.php', ['user' => (isset($user) ? $user : null)]) ?>
    <div class="container">
        <?=$this->section('content')?>
    </div>

    <script src="/js/vendor/bootstrap.min.js"></script>
</body>
</html>
