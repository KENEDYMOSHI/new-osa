<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo</title>
</head>
<style>
body {
    font-family: sans-serif;
}
</style>

<body>
    <pre>

    <?php
    print_r($details);
    $task = '';

    ?>
    <?php foreach ($tasks as $work) : ?>
        <?php $task .= $work['the_group'] ?>
    <?php endforeach; ?>


    <?php foreach ($details as $item => $g) : ?>
    <?php foreach ($g as $i) : ?>
    <?php if ($i['group_name'] == $task) : ?>

    <h3><?= $i['officer_id'] ?></h3>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endforeach; ?>

    </pre>

</body>

</html>