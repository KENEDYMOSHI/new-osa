<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url() ?>/assets/images/wma1.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/adminlte.min">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/bootstrap.css">
    <script src="<?= base_url() ?>/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/dist/js/bootstrap.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title><?= $page['title'] ?></title>
    <style>
        body {
            overflow: hidden;
            /* height: 100%; */
            font-family: 'Nunito', sans-serif;
        }

        h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        /* 





*/


        .authentication {
            margin-top: 3.4rem;
            height: 350px;
            /* background: green; */
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            padding: 0;
            border-radius: 5px;
            /* width: 60vw; */
            overflow: hidden;
            /* margin-right: -1rem; */
        }

        /* #box-x {
            display: block;
            width: 500px !important;
        } */

        .slide {
            height: 350px;
            overflow: hidden;
        }

        .slide img {
            height: 350px;
            width: 100%;
            object-fit: cover;
            overflow: hidden;
        }

        .form-box {
            padding-right: 2.5rem;
            padding-top: 4rem;
            /* width: 100%; */
        }

        .mg {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }



        @media screen and (max-width:640px) {
            .hide {
                display: none;
            }

            body {
                height: 100%;
            }

            h5 {
                margin: 0;
                font-size: 0.6rem;
            }

            .authentication {
                margin-top: 2rem;
                width: 90vw;
                /* height: 100%; */
                /* background: green; */
                /* overflow: hidden; */
                box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
                padding: 0;
                margin-left: 0;

            }

            .mg {
                padding: 2rem;

            }

            .form-box {
                padding: 2.5rem;

            }

            /* .auth-nav {
                padding: 0;

            } */

            /* #login {
                padding: 2rem;
                di
            } */
        }
    </style>
</head>

<body>
    <?php $pageSession = \CodeIgniter\Config\Services::session(); ?>
    <nav class="auth-nav">
        <img class="auth-logo" src="<?= base_url() ?>/assets/images/emblem.png" alt="">
        <div class="heading text-center">
            <h5>THE UNITED REPUBLIC OF TANZANIA</h5>
            <h5>WEIGHTS AND MEASURES AGENCY</h5>
            <h5>MANAGEMENT INFORMATION SYSTEM (WMA-MIS)</h5>
        </div>
        <img class="auth-logo" src="<?= base_url() ?>/assets/images/wma1.png" alt="">
    </nav>
    <main class="login">
        <main class="login">
            <!-- Carousel -->

            <!-- Login -->
            <section class="login__right elevation-2">
                <div class="panel ">
                    <article class="panel__header">
                        <div class="header__brand">
                            <!-- <div class="header__logo">
                                <img src="<?= base_url() ?>/assets/images/wma1.png" alt="">
                            </div> -->

                        </div>
                    </article>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>


                    <div class="auth-box">


                        <?= form_open() ?>
                        <div class="sign text-center">
                            <p class="sign__input">Update Your Mobile Number </p>


                            <div class="form-group">
                                <input type="mobileNumber" name="mobileNumber" max="10" min="10" class="form-control"
                                    placeholder="Enter Mobile Number" value="<?= set_value('mobileNumber') ?>"
                                    oninput="validateMobileNumber(this)" required>
                                <span class="text-danger"><?= displayError($validation, 'mobileNumber') ?></span>
                            </div>

                        </div>

                        <div class="option">
                            <div class="option__item">
                                <button style="background: #DB611E;" type="submit" class="button">Submit</button>
                            </div>

                        </div>

                        </form>
                    </div>


                </div>
            </section>
        </main>

        <script>
            let baseUrl = '<?= base_url() ?>';
            setInterval(function () {
                $('#message').fadeOut(7000)
            });

            function validateMobileNumber(input) {
                // Remove any non-digit characters
                input.value = input.value.replace(/\D/g, '');

                // Limit to 10 digits
                if (input.value.length > 10) {
                    input.value = input.value.slice(0, 10);
                }
            }

            function checkSession() {
                console.log(baseUrl)
                fetch(baseUrl + '/checkSession') // Replace with the URL to your server-side script that checks the session status
                    .then(response => response.json())
                    .then(data => {
                        const {
                            status,
                            redirectTo
                        } = data
                        if (status === 'inactive') {
                            // Log out the user if the session is inactive
                            window.location.href = redirectTo; // Replace with the URL to your logout controller method
                        } else {
                            console.log(data)
                        }
                    })
                    .catch(error => {
                        console.log('Error checking session status');
                        console.error(error);
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                setInterval(checkSession, 20000); // Check session every 20 seconds
            });


            // Initialize the timer variables
            let inactivityTime = 1; // 3 minutes
            let inactivityTimer;
            let countdownInterval;



            // Function to reset the inactivity timer
            function resetInactivityTimer() {
                // Clear the previous timers, if any
                clearTimeout(inactivityTimer);
                clearInterval(countdownInterval);

                // Start a new timer
                inactivityTimer = setTimeout(function () {
                    // The user is inactive, show a SweetAlert countdown and then send a fetch request to the logout route
                    var countdownTime = 60; // seconds
                    countdownInterval = setInterval(function () {
                        swal({
                            title: `You will be logged out in ${countdownTime} seconds`,
                            timer: 1000,
                            icon: 'warning',
                            buttons: {
                                cancel: "Cancel",

                            },
                            onBeforeOpen: function () {
                                swal.showLoading();
                            }
                        });
                        countdownTime--;

                        if (countdownTime < 0) {
                            clearInterval(countdownInterval);
                            swal.close();
                            fetch(baseUrl + 'destroySession')
                                .then(function (response) {
                                    return response.json();
                                })
                                .then(function (data) {
                                    // If the response status is 1, redirect to the login page
                                    if (data.status === 1) {
                                        window.location.href = baseUrl + '/';
                                    }
                                })
                                .catch(function (error) {
                                    console.error(error);
                                });
                        }
                    }, 1000);
                }, inactivityTime * 60 * 1000); // Convert to milliseconds
            }

            // Add event listeners to reset the timer when the user interacts with the page
            document.addEventListener('mousemove', resetInactivityTimer);
            document.addEventListener('keydown', resetInactivityTimer);

            // Add an event listener to clear the countdown interval when the user moves their mouse over the page
            document.addEventListener('mousemove', function () {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    swal.close();
                }
            });
        </script>

</body>

</html>