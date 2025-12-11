<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker Printing</title>
    <style>
        body {
            margin: 0;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        @media print {
            body {
                margin: 0;
            }

            body {
                margin: 0;
            }

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                margin: 0 auto;
            }

        }



        
    </style>
</head>

<body>

    <img src="<?= $stickerUrl ?>" alt="Sticker">

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>