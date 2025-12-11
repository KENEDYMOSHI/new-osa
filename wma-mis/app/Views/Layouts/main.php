<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title><?= isset($title) ? $title : 'Default Title' ?></title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">WMA-MIS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">


                    <!-- <ul>
                        <li><a href="blog">Blob</a></li>
                        <li><a href="blog/post">Sigle Post</a></li>
                    </ul> -->
                    <li class="nav-item active">
                        <a class="nav-link" href="blog">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog/post">Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog/new">New Activity</a>
                    </li>


                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <?= $this->renderSection('content'); ?>
    </div>

    <script src="assets/js/bootstrap.js"></script>
</body>

</html>