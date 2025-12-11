<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            img {
                width: 100vw;
                height: 100vh;
                /* object-fit: contain; */
            }
        }
    </style>
</head>

<body>
    <img src="<?= $fileUrl ?>" alt="">
</body>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</html>
