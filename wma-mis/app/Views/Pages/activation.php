<?= $this->include('layouts/head.php'); ?>

<body>

    <div class="container-fluid">
        <?php if (isset($error)) : ?>
        <br><br>
        <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($Success)) : ?>
        <br><br>
        <div class="alert alert-success text-center"><?= $Success ?></div>
        <?php endif; ?>
    </div>
</body>

</html>