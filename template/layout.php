<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
        <title><?php echo $title;?></title>
        <link rel="stylesheet" href="/static/base.css">
        <link rel="stylesheet" href="/static/github-markdown.css">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <style>
            body {
                min-width: 200px;
                max-width: 960px;
                margin: 0 auto;
                padding: 30px;
            }
        </style>
    </head>
    <body>
    <?php $this->render('breadcrumb.php', ['breadcrumb' => $breadcrumb]);?>
    <?php $this->block('content');?>
    <div class="footer">Powered by <a href="http://github.com/ddliu/airdoc">airdoc</a></div>
</body>
</html>