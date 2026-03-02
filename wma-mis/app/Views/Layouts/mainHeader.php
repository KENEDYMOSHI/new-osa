<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= isset($page) ? $page['title'] : 'Logs'; ?></title>

    <link rel="shortcut icon" href="<?= base_url() ?>assets/images/wma1.png" type="image/x-icon">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- iCheck for checkboxes and radio inputs -->

    <!-- <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
        integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- <script src="<?= base_url() ?>/plugins/moment/moment.min.js"></script> -->
    <!-- <script src="<?= base_url() ?>/plugins/daterangepicker.js"></script> -->

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Jquery Validator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"
        integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?= base_url() ?>dist/js/additional-methods.min.js"></script>
    <!-- <script src="<?= base_url() ?>dist/js/jquery.validate.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script> -->

    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>

    <!-- Include Parsley.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"
        integrity="sha512-eyHL1atYNycXNXZMDndxrDhNAegH2BDWt1TmkXJPoGf1WLlNYt08CSjkqF5lnCRmdm3IrkHid8s2jOUY4NIZVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/progress.css">
    <!-- Google Font: Source Sans Pro -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> -->


    <!-- Data Tables -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- summernote -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/timePicker.css">

    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- ============================================= -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"> -->
    <!-- =============== -->
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- daterange picker -->
    <link rel="stylesheet" href="<?= base_url() ?>/plugins/daterangepicker/daterangepicker.css">

    <!-- light box -->
    <!-- <link href="path/to/lightbox.css" rel="stylesheet" />
    <script src="path/to/lightbox.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/axiosdist/axios.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
    <script src="<?= base_url() ?>dist/js/sweetalert.js"></script>

    <script src="<?= base_url() ?>dist/js/apexCharts.js"></script>

    <!-- Global JavaScript Variables -->
    <script>
        // Global base URL - available throughout the application
        if (typeof baseUrl === 'undefined') {
            var baseUrl = '<?= rtrim(base_url(), "/") ?>';
        }
    </script>

    <!-- WCF Calculator Utility -->

    <!-- <script src="<?= base_url() ?>dist/js/prepackage.js"></script> -->


    <!-- input Mask -->

    <!-- <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script> -->
    <!-- Qr code library -->
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>


    <script>
        (g => {
            var h, a, k, p = "The Google Maps JavaScript API",
                c = "google",
                l = "importLibrary",
                q = "__ib__",
                m = document,
                b = window;
            b = b[c] || (b[c] = {});
            var d = b.maps || (b.maps = {}),
                r = new Set,
                e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
                    await (a = m.createElement("script"));
                    e.set("libraries", [...r] + "");
                    for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                    e.set("callback", c + ".maps." + q);
                    a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                    d[q] = f;
                    a.onerror = () => h = n(Error(p + " could not load."));
                    a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                    m.head.append(a)
                }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })
            ({
                key: "AIzaSyB1205awEuF9whGrPY379bbknVzHOdNwWo",
                v: "beta"
            });

        //validation library override
        $.validator.prototype.checkForm = function () {
            this.prepareForm();
            for (var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++) {
                if (this.findByName(elements[i].name).length != undefined && this.findByName(elements[i].name).length > 1) {
                    for (var cnt = 0; cnt < this.findByName(elements[i].name).length; cnt++) {
                        this.check(this.findByName(elements[i].name)[cnt]);
                    }
                } else {
                    this.check(elements[i]);
                }
            }
            return this.valid();
        };


        function submitInProgress(button) {
            let spinner = button.querySelector("#spinner")
            spinner.style.display = 'inline-block'
            button.disabled = true

        }

        //remove loading animation
        function submitDone(button) {
            let spinner = button.querySelector("#spinner")
            spinner.style.display = 'none'
            button.disabled = false
        }




    </script>


    <style>
        .bg-primary {
            background: #dc3545;
            color: #333;
        }

        .select2bs4 {
            width: 100%;
        }

        ::selection {
            background: coral;
            /* WebKit/Blink Browsers */
        }

        ::-moz-selection {
            background: coral;
            /* Gecko Browsers */
        }

        .error {

            color: #dc3545;
        }

        .swal-button {
            padding: 7px 19px;
            border-radius: 2px;
            background-color: #B64B11;
            font-size: 12px;
            border: 1px solid #e1e1e1;
            text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.3);
        }

        .swal-button-container .swal-button--danger {

            background: #B64B11 !important;

        }


        .swal-button--cancel {

            background-color: #333 !important;
            color: #fff !important;

        }

        .swal-title {
            font-weight: 300;
        }


        .swal-button--confirm:hover,
        .swal-button-container .swal-button--danger:hover {
            background-color: #e56824 !important;
            border-radius: 2px;
        }

        .swal-button--cancel:hover {
            background-color: #555 !important;
            border-radius: 2px;
        }

        /* 
        .swal-button-container .swal-button--danger:hover {

           
           
            background-color: #B64B11 !important;
        } */

        .tableData {
            padding: 0px !important
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        /* 
        .modal-body-wrapper {
            overflow-y: scroll;
            height: 60vh;
        } */

        @media print {


            .modal-body-wrapper {
                position: relative;
                page-break-inside: avoid !important;

            }


            .bill-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            #modal-body {
                page-break-inside: avoid !important;
                /* margin-top: -67.5rem; */
                position: absolute;
                top: 0;
                font-size: 1.2rem;
                margin: 0 20px;

                -webkit-margin: 0px 20px;
                -moz-margin: 0px 20px;
                -ms-margin: 0px 20px;
                -o-margin: 0px 20px;


                padding: 0;
                /* background-color: #dc3545 !important; */
            }

            body * {
                /* padding: 0;
                margin: 0; */
                visibility: hidden;
                /* display: none; */
                page-break-inside: avoid !important;

            }

            #modal-body,
            #modal-body * {
                /* font-size: 1.3rem; */
                page-break-inside: avoid !important;
                /* margin-top: -80px; */
                /* padding: 0; */
                visibility: visible;
                /* display: block; */

            }





        }

        .shade {
            background-color: #555;
            color: #fff;
        }

        select option:hover {
            background: green !important;
            /*change this to the desired color*/
        }

        .mapBox {
            width: 775;
            height: 400px;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        .location-box {
            background: #ffffff;
            padding: 0.9rem 0.7rem;
            margin-top: 0.6rem;
            border-radius: 3px;
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px,
                rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
        }

        label.must::after {
            content: "*";
            color: red;
        }
    </style>



</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="far fa-bars"></i></a>
                </li>



            </ul>


            <img class="avatar  " src="<?= base_url() ?>/assets/images/emblem.png" alt="Logo">
            <?= tokenField() ?>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown d-sm-inline-block mr-3" id="notification-dropdown-container">
                    <a href="#" class="nav-link notification-toggle" data-toggle="dropdown"
                       style="position: relative; display: inline-flex; align-items: center; justify-content: center; padding: 6px; color: #9ca3af; transition: color 0.2s; margin-top: 2px;">
                        <!-- Red badge -->
                        <span id="unread-count-badge"
                              style="display: none; position: absolute; top: -2px; right: -4px;
                                     height: 22px; min-width: 22px;
                                     align-items: center; justify-content: center;
                                     border-radius: 50%; background: #e53e3e;
                                     padding: 0 5px; font-size: 12px; font-weight: 700;
                                     color: white; border: 2px solid #1a252f;
                                     line-height: 1; z-index: 1;">0</span>
                        <!-- Classic Bell Icon -->
                        <i class="fas fa-bell" id="bell-icon"
                           style="font-size: 22px; color: #8d99ae; transition: color 0.2s;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 350px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 0; min-width:350px; left: inherit; right: 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">
                            <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #1f2937;">Notifications</h5>
                            <span id="unread-count-text" style="display: none; background: #fee2e2; color: #b91c1c; font-size: 12px; font-weight: 500; padding: 2px 8px; border-radius: 4px; border: 1px solid #fca5a5;">0 new</span>
                        </div>
                        <div id="notifications-list" style="max-height: 350px; overflow-y: auto;">
                            <!-- Items go here -->
                        </div>
                        <div style="padding: 12px; border-top: 1px solid #f3f4f6; text-align: center;">
                            <a href="<?= base_url('wmaNotifications') ?>" style="font-size: 14px; font-weight: 500; color: #e11d48; text-decoration: none;" onmouseover="this.style.color='#be123c';" onmouseout="this.style.color='#e11d48';">View All Notifications</a>
                        </div>
                    </div>
                </li>

                <!-- User Profile Dropdown Menu -->
                <li class="nav-item dropdown d-sm-inline-block mr-4">

                    <a href="#" data-toggle="dropdown">
                        <div class="img-box">

                            <img class="avatar  " src="<?= base_url() ?>/assets/images/wma1.png" alt="Logo">
                        </div>

                    </a>

                </li>

            </ul>
        </nav>
        
        <style>
            @keyframes ring-bell {
              0% { transform: rotate(0); }
              15% { transform: rotate(15deg); }
              30% { transform: rotate(-10deg); }
              45% { transform: rotate(5deg); }
              60% { transform: rotate(-5deg); }
              75% { transform: rotate(2deg); }
              100% { transform: rotate(0); }
            }
            @keyframes badge-bounce-in {
              0% { transform: scale(0); opacity: 0; }
              80% { transform: scale(1.1); opacity: 1; }
              100% { transform: scale(1); opacity: 1; }
            }
            .notification-toggle:hover #bell-icon {
              animation: ring-bell 1s ease-in-out;
              transform-origin: top center;
            }
            .badge-animated {
              animation: badge-bounce-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var baseUrl = '<?= rtrim(base_url(), "/") ?>';
                
                function fetchNotifications() {
                    fetch(baseUrl + '/wmaNotificationsAjax')
                        .then(response => response.json())
                        .then(data => {
                            var notifications = data.notifications || (Array.isArray(data) ? data : []);
                            var unreadCount   = (data.unread_count !== undefined) ? data.unread_count : notifications.filter(n => !n.is_read).length;
                            updateNotificationUI(notifications, unreadCount);
                        })
                        .catch(err => console.error('Failed to fetch WMA notifications', err));
                }
                
                function updateNotificationUI(notifications, unread) {
                    var badge = document.getElementById('unread-count-badge');
                    var spanText = document.getElementById('unread-count-text');
                    var bell = document.getElementById('bell-icon');
                    var list = document.getElementById('notifications-list');
                    
                    if (unread > 0) {
                        badge.style.display = 'flex';
                        badge.classList.add('badge-animated');
                        badge.textContent = unread > 99 ? '99+' : unread;
                        spanText.style.display = 'block';
                        spanText.textContent = unread + ' new';
                        bell.style.color = '#4a5568'; // darker when there are unread
                    } else {
                        badge.style.display = 'none';
                        spanText.style.display = 'none';
                        bell.style.color = '#8d99ae'; // light grey when all read
                    }
                    
                    if (notifications.length === 0) {
                        list.innerHTML = '<div style="padding: 32px 16px; text-align: center; color: #6b7280; font-size: 14px;">No notifications yet</div>';
                    } else {
                        var html = '';
                        notifications.forEach(function(notif) {
                            var isUnread = !notif.is_read;
                            var date = new Date(notif.created_at).toLocaleDateString();
                            var bgClass = isUnread ? 'background-color: #eff6ff;' : 'background-color: transparent;';
                            var unreadDot = isUnread ? '<span style="position: absolute; top: 0; right: 0; display: block; width: 10px; height: 10px; border-radius: 50%; background-color: #f97316; border: 1.5px solid white;"></span>' : '';
                            
                            var iconSvg = notif.type === 'document_reuploaded'
                                ? '<div style="color: #f97316;"><i class="fas fa-upload" style="font-size:16px;"></i></div>'
                                : '<div style="color: #22c55e;"><i class="fas fa-check-circle" style="font-size:16px;"></i></div>';
                                
                            html += '<a href="#" class="notification-item" data-id="'+notif.id+'" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; text-decoration: none; color: inherit; transition: background-color 0.2s; ' + bgClass + '" onmouseover="this.style.backgroundColor=\'#f3f4f6\';" onmouseout="this.style.backgroundColor=\'' + (isUnread ? '#eff6ff' : 'transparent') + '\';">' +
                                '<div style="position: relative; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background-color: #f3f4f6; flex-shrink: 0;">' +
                                iconSvg + unreadDot +
                                '</div>' +
                                '<div style="flex: 1; min-width: 0;">' +
                                '<p style="margin: 0 0 4px 0; font-size: 14px; font-weight: 500; color: #1f2937;">' + notif.title + '</p>' +
                                '<p style="margin: 0 0 4px 0; font-size: 12px; color: #6b7280; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">' + notif.message + '</p>' +
                                '<p style="margin: 0; font-size: 11px; color: #9ca3af;">' + date + '</p>' +
                                '</div>' +
                                '</a>';
                        });
                        list.innerHTML = html;
                        
                        list.querySelectorAll('.notification-item').forEach(function(item) {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                var id = this.getAttribute('data-id');
                                var notif = notifications.find(n => n.id == id);
                                if (notif && !notif.is_read) {
                                    fetch(baseUrl + '/wmaMarkNotificationRead/' + id, {method: 'GET'})
                                        .then(() => { fetchNotifications(); });
                                }
                                window.location.href = baseUrl + '/wmaNotifications';
                            });
                        });
                    }
                }
                
                fetchNotifications();
                setInterval(fetchNotifications, 30000);
            });
        </script>
        <?php $user = auth()->user() ?>