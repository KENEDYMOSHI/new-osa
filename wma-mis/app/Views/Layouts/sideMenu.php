<?php $user = auth()->user(); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">

        <span class="brand-text font-weight-bold ml-3"> WMA-MIS</span>
        <p class="text-light mb-1 mx-3" style="font-size: 13px;"><?= centerName() ?></p>


    </a>

    <!-- Sidebar -->
    <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-4">
            <ul class="nav nav-pills nav-sidebar child-indent flex-column" data-widget="treeview" role="menu"
                data-accordion="true">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <!-- entire link open -->

                <li class="nav-item ">
                    <a href="#" class="nav-link ">
                        <img src="<?= base_url('assets/images/avatar.png') ?>" class="img-circle elevation-1" alt=""
                            width="25">

                        <p>
                            <?= $user->username ?>
                            <i class="right fal fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('changePassword') ?>" class="nav-link">
                                <i class="fal fa-key nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('profile') ?>" class="nav-link">
                                <!-- <i class="fal fa-list-alt nav-icon"></i> -->
                                <i class="fal fa-user nav-icon"></i>
                                <p>My Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('signout') ?>" class="nav-link">
                                <!-- <i class="fal fa-list-alt nav-icon"></i> -->
                                <i class="fal fa-sign-out-alt nav-icon"></i>
                                <p>Log Out</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link ">
                        <i class="fal fa-tachometer-alt nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Dashboard

                        </p>
                    </a>
                </li>
                <?php if ($user->inGroup('superadmin')): ?>
                    <li class="nav-item ">
                        <a href="<?= base_url() ?>admin/setting" class="nav-link ">
                            <i class="fal fa-cog nav-icon"></i>
                            <p>
                                Settings
                            </p>
                        </a>

                    </li>
                    <?php if (setting('System.env') == 'testing'): ?>

                        <!-- <li class="nav-item ">
                        <a href="<?= base_url() ?>paymentSimulator" class="nav-link ">
                            <i class="fal fa-money-bill nav-icon"></i>
                            <p>
                                Payments Simulator
                            </p>
                        </a>

                    </li> -->
                    <?php endif; ?>
                <?php endif; ?>


                <li class="nav-item ">
                    <a href="<?= base_url() ?>certificate" class="nav-link ">
                        <i class="fal fa-file-certificate nav-icon"></i>
                        <p>
                            Certificates
                        </p>
                    </a>

                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fal fa-layer-group nav-icon"></i>
                        <p>
                            OSA
                            <i class="right fal fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() ?>osaDashboard" class="nav-link">
                                <i class="fal fa-tachometer-alt-fast nav-icon"></i>
                                <p>OSA Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>initialApplicationApproval" class="nav-link">
                                <i class="fal fa-file-check nav-icon"></i>
                                <p>Initial Application Approval</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>applicationAproval" class="nav-link">
                                <i class="fal fa-user-check nav-icon"></i>
                                <p>License Approval</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>examRemark" class="nav-link">
                                <i class="fal fa-file-edit nav-icon"></i>
                                <p>Exam Remark</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>licenseReport" class="nav-link">
                                <i class="fal fa-file-chart-line nav-icon"></i>
                                <p>License Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>licenseBillReport" class="nav-link">
                                <i class="fal fa-file-invoice-dollar nav-icon"></i>
                                <p>License Bill Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item ">
                    <a href="<?= base_url() ?>applicationVerification" class="nav-link ">
                        <i class="fal fa-check nav-icon"></i>
                        <p>
                            Applicants Verification
                        </p>
                    </a>

                </li>

                <?php if ($user->inGroup('superadmin', 'admin', 'officer', 'manager')): ?>


                    <!-- <li class="nav-item ">
                    <a href="<?= base_url() ?>certificate" class="nav-link ">
                        <i class="fal fa-file-certificate nav-icon"></i>
                        <p>
                            Certificates
                        </p>
                    </a>

                </li> -->

                <?php endif; ?>


                <li class="nav-item">
                    <a href="<?= base_url('search') ?>" class="nav-link ">
                        <i class="fal fa-search nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Search

                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('sticker') ?>" class="nav-link ">
                        <i class="fal fa-tags nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Sticker

                        </p>
                    </a>
                </li>


                <li class="nav-item <?= url_is('reports') || url_is('GeolocationReport') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link">
                        <i class="fal fa-file-spreadsheet nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Reports
                            <i class="right fal fa-angle-left"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="<?= base_url('reports') ?>"
                                class="nav-link <?= url_is('reports') ? 'active' : '' ?> ">

                                <i class="fal fa-file-chart-pie nav-icon"></i>
                                <p>Collection Reports</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('GeolocationReport') ?>" class="nav-link">

                                <i class="fal fa-map-marked-alt nav-icon"></i>
                                <p>Geolocation Report</p>
                            </a>
                        </li>
                        <!-- //TODO remove the group and use role based access -->


                        <li class="nav-item">
                            <a href="<?= base_url('receivableSummary') ?>" class="nav-link">

                                <i class="fal fa-chart-line-down nav-icon"></i>
                                <p>Receivables Summary</p>
                            </a>
                        </li>

                        <?php if ($user->inGroup('officer', 'manager', 'admin', 'superadmin', 'headofsection') || $user->hasPermission('report.collectionSummary')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('collectionSummary') ?>" class="nav-link ">
                                    <i class="fal fa-file-alt nav-icon"></i>
                                    <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                                    <p>
                                        Collection Summary

                                    </p>
                                </a>


                            </li>
                        <?php endif; ?>
                        <?php if ($user->hasPermission('report.variance-analysis')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('varianceAnalysis') ?>" class="nav-link ">
                                    <i class="fal fa-chart-bar nav-icon"></i>
                                    <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                                    <p>
                                        Variance Analysis

                                    </p>
                                </a>


                            </li>
                        <?php endif; ?>
                        <?php if ($user->hasPermission('report.trCollection') || $user->inGroup('accountant', 'accountant-hq', 'admin', 'superadmin')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('trCollection') ?>" class="nav-link ">
                                    <i class="fal fa-sack-dollar nav-icon"></i>
                                    <p>
                                        Tr Collection Report

                                    </p>
                                </a>


                            </li>
                        <?php endif; ?>







                        <?php if (!$user->inGroup('surveillance')): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link ">
                                    <i class="fal fa-folders nav-icon"></i>
                                    <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                                    <p>
                                        Instrument Report
                                        <i class="right fal fa-angle-left"></i>

                                    </p>
                                </a>
                                <ul class="nav nav-treeview">

                                    <li class="nav-item ">
                                        <a href="<?= base_url('rejected') ?>" class="nav-link ">
                                            <i class="fal fa-file-times nav-icon"></i>
                                            <p>
                                                Rejected Report
                                                <!-- <i class="right fal fa-angle-left"></i> -->
                                            </p>
                                        </a>

                                    </li>
                                    <li class="nav-item ">
                                        <a href="<?= base_url('condemned') ?>" class="nav-link ">
                                            <i class="fal fa-ban nav-icon"></i>
                                            <p>
                                                Condemned Report
                                                <!-- <i class="right fal fa-angle-left"></i> -->
                                            </p>
                                        </a>

                                    </li>
                                    <li class="nav-item ">
                                        <a href="<?= base_url('adjusted') ?>" class="nav-link ">
                                            <i class="fal fa-tools nav-icon"></i>
                                            <p>
                                                Adjustment Report

                                            </p>
                                        </a>

                                    </li>
                                    <li class="nav-item ">
                                        <a href="<?= base_url('stampedInstruments') ?>" class="nav-link ">
                                            <i class="fal fa-stamp nav-icon"></i>
                                            <p>
                                                Stamped Report
                                                <!-- <i class="right fal fa-angle-left"></i> -->
                                            </p>
                                        </a>

                                    </li>


                                </ul>
                            </li>
                        <?php endif; ?>



                    </ul>
                </li>




                <?php if ($user->hasPermission('estimates.manage')): ?>
                    <li class="nav-item ">
                        <a href="<?= base_url('estimates') ?>" class="nav-link ">
                            <i class="fal fa-chart-pie-alt nav-icon"></i>
                            <p>
                                Collection Estimates
                            </p>
                        </a>

                    </li>
                <?php endif; ?>

                <?php if ($user->hasPermission('estimates.manage')): ?>
                    <li class="nav-item ">
                        <a href="<?= base_url('instrumentEstimates') ?>" class="nav-link ">
                            <i class="fal fa-project-diagram nav-icon"></i>
                            <p>
                                Instruments Estimates
                            </p>
                        </a>

                    </li>
                <?php endif; ?>
                <?php if ($user->inGroup('manager') || $user->hasPermission('activity-estimates.manage')): ?>
                    <li class="nav-item ">
                        <a href="<?= base_url('activityEstimates') ?>" class="nav-link ">
                            <i class="fal fa-chart-scatter nav-icon"></i>
                            <p>
                                Activity Estimates
                            </p>
                        </a>

                    </li>
                <?php endif; ?>












                <li class="nav-item >">
                    <a href="#" class="nav-link ">
                        <i class="fal fa-money-bill-alt nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Bill Management
                            <i class="right fal fa-angle-left"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if ($user->inGroup('superadmin', 'admin')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('billCreationCombined') ?>" class="nav-link">

                                    <i class="fal fa-file-plus nav-icon"></i>
                                    <p>Create Combined Bill</p>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($user->can('bill.create') || $user->hasPermission('bill.create')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('billCreation') ?>" class="nav-link">

                                    <i class="fal fa-file-plus nav-icon"></i>
                                    <p>Create Bill</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a href="<?= base_url('billManagement') ?>" class="nav-link">

                                <i class="fal fa-file-search nav-icon"></i>
                                <p>Search Bill</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('payments') ?>" class="nav-link  ">
                                <i class="fal fa-receipt nav-icon"></i>
                                <p>
                                    Payment Receipt
                                    <!-- <i class="right fal fa-angle-left"></i> -->
                                </p>
                            </a>

                        </li>
                        <!-- <?php if ($user->can('reconciliation.access')): ?>
                            <li class="nav-item ">
                                <a href="#" class="nav-link ">
                                    <i class="fal fa-file-invoice nav-icon"></i>
                                    <p>
                                        Payment Reconciliation
                                       
                                    </p>
                                </a>

                            </li>
                           
                        <?php endif; ?> -->
                        <?php if ($user->can('admin-recon.access')): ?>

                            <li class="nav-item ">
                                <a href="<?= base_url('reconciliation') ?>" class="nav-link ">
                                    <i class="fal fa-file-invoice nav-icon"></i>
                                    <p>
                                        Reconciliation
                                        <!-- <i class="right fal fa-angle-left"></i> -->
                                    </p>
                                </a>

                            </li>
                        <?php endif; ?>

                        <li class="nav-item ">
                            <a href="<?= base_url('cancelledBills') ?>" class="nav-link ">
                                <i class="fal fa-ban nav-icon"></i>
                                <p>
                                    Cancelled Bills
                                    <!-- <i class="right fal fa-angle-left"></i> -->
                                </p>
                            </a>

                        </li>
                        <?php if ($user->inGroup('manager', 'accountant') || $user->hasPermission('bill.cancelapproval')): ?>
                            <li class="nav-item ">
                                <a href="<?= base_url('cancellationRequests') ?>" class="nav-link ">
                                    <i class="fal fa-inbox-in nav-icon"></i>
                                    <p>
                                        Cancellation Requests
                                        <!-- <i class="right fal fa-angle-left"></i> -->
                                    </p>
                                </a>

                            </li>

                        <?php endif; ?>



                    </ul>
                </li>


                <!-- manager links -->
                <?php if ($user->inGroup('manager')): ?>
                    <!-- <li class="nav-item">
                        <a href="#" class="nav-link ">

                            <i class="fal fa-folder-tree nav-icon"></i>
                          
                            <p>
                                Service Application
                                <i class="right fal fa-angle-left"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="<?= base_url('license-applications') ?>" class="nav-link">


                                    <i class="far fa-file-certificate nav-icon"></i>
                                    <p>License Applications</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('service-requests') ?>" class="nav-link">

                                    <i class="far fa-file-check nav-icon"></i>
                                    <p>Service Request</p>
                                </a>
                            </li>



                        </ul>
                    </li> -->
                    <?php if ($user->inGroup('surveillance', 'admin')): ?>
                        <li class="nav-item ">
                            <a href="<?= base_url('instrumentsTarget') ?>" class="nav-link ">
                                <i class="fal fa-bullseye-arrow nav-icon"></i>
                                <p>
                                    Instrument Target
                                </p>
                            </a>

                        </li>
                    <?php endif; ?>



                    <li class="nav-item">
                        <a href="<?= base_url('assignTask') ?>" class="nav-link ">
                            <i class="fal fa-layer-plus nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                Assign A Task

                            </p>
                        </a>


                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('viewTasks') ?>" class="nav-link ">
                            <i class="fal fa-tasks nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                View Tasks

                            </p>
                        </a>
                    </li>

                <?php endif; ?>
                <!-- end of manager links -->



                <?php if ($user->inGroup('superadmin', 'admin')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/users') ?>" class="nav-link ">
                            <i class="fal fa-users nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                Users

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/backup') ?>" class="nav-link ">
                            <i class="fal fa-database nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                Database Backup

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/posManagement') ?>" class="nav-link ">
                            <i class="far fa-mobile-android nav-icon"></i>
                            <p>
                                Pos Management

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/activityLogs') ?>" class="nav-link ">
                            <i class="far fa-history nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                User Activity Logs

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/logs') ?>" class="nav-link ">
                            <i class="far fa-file-code nav-icon"></i>
                            <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                            <p>
                                System Logs

                            </p>
                        </a>
                    </li>


                <?php endif; ?>

                <?php if (!$user->inGroup('officer', 'manager')): ?>
                    <li class="nav-item ">
                        <a href="<?= base_url('instrumentsTarget') ?>" class="nav-link ">
                            <i class="fal fa-bullseye-arrow nav-icon"></i>
                            <p>
                                Collection Target
                            </p>
                        </a>

                    </li>


                <?php endif; ?>


                <?php $center = $user->inGroup('officer', 'manager') ? $user->collection_center : 'all' ?>

                <li class="nav-item ">
                    <a href="#" class="nav-link">
                        <i class="fal fa-ruler-triangle nav-icon"></i>
                        <p>
                            Measuring Instruments
                            <i class="right fal fa-angle-left"></i>

                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <!-- ******************** -->
                        <li class="nav-item ">
                            <a href="#" class="nav-link">
                                <i class="fal fa-truck-container nav-icon"></i>
                                <p>
                                    Vehicle Tank Verification
                                    <i class="right fal fa-angle-left"></i>

                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <?php if ($user->inGroup('officer', 'manager')): ?>

                                    <li class="nav-item">
                                        <a href="<?= base_url('addVehicleTank') ?>" class="nav-link">
                                            <i class="fal fa-plus nav-icon"></i>
                                            <p>Add Vehicle Tank</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url('vehicleCalibrationChart') ?>" class="nav-link">
                                            <i class="fal fa-file-spreadsheet nav-icon"></i>
                                            <p>Calibration Chart</p>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="nav-item">
                                    <a href="<?= base_url('listVehicleTanks/' . $center) ?>" class="nav-link">
                                        <!-- <i class="fal fa-list nav-icon"></i> -->
                                        <i class="fal fa-clipboard-list-check nav-icon"></i>
                                        <p>View Registered Tanks</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- ******************* -->
                        <li class="nav-item ">
                            <a href="#" class="nav-link">
                                <i class="fal fa-truck nav-icon"></i>
                                <p>
                                    Sandy & Ballast Lorries
                                    <i class="right fal fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <?php if ($user->inGroup('officer', 'manager')): ?>

                                    <li class="nav-item">
                                        <a href="<?= base_url('addLorry') ?>" class="nav-link">
                                            <i class="fal fa-plus nav-icon"></i>
                                            <p>Add Lorry</p>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="nav-item">
                                    <a href="<?= base_url('listLorries/' . $center) ?>" class="nav-link">
                                        <i class="fal fa-clipboard-list-check nav-icon"></i>
                                        <p>Registered Lorries</p>
                                    </a>
                                </li>

                            </ul>
                        </li>



                        <li class="nav-item ">
                            <a href="#" class="nav-link">
                                <i class="fal fa-ring nav-icon"></i>
                                <p>
                                    Water Meter
                                    <i class="right fal fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <?php if ($user->inGroup('officer', 'manager')): ?>

                                    <li class="nav-item">
                                        <a href="<?= base_url('addWaterMeter') ?>" class="nav-link">
                                            <i class="fal fa-plus nav-icon"></i>
                                            <p>Add Water Meter</p>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="nav-item">
                                    <a href="<?= base_url('WaterMeterList/' . $center) ?>" class="nav-link">
                                        <i class="fal fa-clipboard-list-check nav-icon"></i>
                                        <p>Registered Water Meters</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item ">
                            <a href="#" class="nav-link">
                                <i class="fal fa-box-alt nav-icon"></i>
                                <p>
                                    Pre Package
                                    <i class="right fal fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php if ($user->inGroup('officer', 'manager')): ?>

                                    <li class="nav-item">
                                        <a href="<?= base_url('prePackage') ?>" class="nav-link">
                                            <i class="fal fa-plus nav-icon"></i>
                                            <p>Add Prepackage</p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('registeredPrepackages/' . $center) ?>" class="nav-link">
                                        <i class="fal fa-clipboard-list-check nav-icon"></i>
                                        <p>Registered Prepackage</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('imported') ?>" class="nav-link">

                                        <i class="fal fa-hand-holding-box nav-icon"></i>
                                        <p>Prepackage(Imported)</p>
                                    </a>
                                </li>



                            </ul>
                        </li>
                    </ul>
                </li>


                <li class="nav-item ">
                    <a href="#" class="nav-link">
                        <i class="fal fa-ship nav-icon"></i>
                        <p>
                            Metrological Supervision
                            <i class="right fal fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php $x = true ?>
                        <?php if ($x): ?>


                            <li class="nav-item">
                                <a href="<?= base_url('metrology/vessels') ?>"
                                    class="nav-link <?= url_is('metrology/vessels') ? 'active' : '' ?>">
                                    <i class="fal fa-ship nav-icon"></i>
                                    <p>Vessels</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="<?= base_url('metrology/voyages') ?>"
                                    class="nav-link <?= url_is('metrological/voyages') ? 'active' : '' ?>">
                                    <i class="fal fa-route nav-icon"></i>
                                    <p>Voyages</p>
                                </a>
                            </li>

                        <?php endif; ?>


                        <!-- ******************* -->
                        <li class="nav-item">
                            <a href="<?= base_url('metrology/settings') ?>" class="nav-link">
                                <i class="fal fa-cog nav-icon"></i>
                                <p>Settings</p>
                            </a>
                        </li>




                    </ul>
                </li>


                <li class="nav-item">
                    <a href="<?= base_url('metrological/VesselReports') ?>" class="nav-link">
                        <i class="fal fa-file nav-icon"></i>
                        <p>Vessel Reports</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('metrological/report-two') ?>" class="nav-link">
                        <i class="fal fa-chart-line nav-icon"></i>
                        <p>Report Two</p>
                    </a>
                </li>






                <!-- <li class="nav-item ">
                    <a href="#" class="nav-link">
                        <i class="fal fa-ship nav-icon"></i>
                        <p>
                            Metrological Supervision
                            <i class="right fal fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fal fa-ship nav-icon"></i>
                                <p>
                                    OnBoard Petroleum
                                    <i class="right fal fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>timeLog" class="nav-link">
                                        <i class="fal fa-clock nav-icon"></i>
                                        <p>Time Log</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>documents" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Documents</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>ullageBeforeDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Ullage Before Discharging</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?= base_url() ?>ullageAfterDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Ullage After Discharging</p>
                                    </a>
                                </li>
                               
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>certificateOfQuantity" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Certificate Of Quantity</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>noteOfFactBeforeDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Note Of Fact Before</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>noteOfFactAfterDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Note Of Fact After</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>dischargingSequence" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Discharging Sequence</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>lineDisplacement" class="nav-link">
                                        <i class="fal fa-draw-circle nav-icon"></i>
                                        <p>Line Displacement</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>provisionalReport" class="nav-link">
                                        <i class="fal fa-file-chart-line nav-icon"></i>
                                        <p>Provisional Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>dischargeOrder" class="nav-link">
                                        <i class="fal fa-chart-network nav-icon"></i>
                                        <p>Discharge Order</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>pressureLog" class="nav-link">
                                        <i class="fal fa-clipboard-list nav-icon"></i>
                                        <p>Pressure Log</p>
                                    </a>
                                </li>
                              


                            </ul>

                        </li>

                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fal fa-house-flood nav-icon"></i>
                                <p>
                                    Offshore Petroleum
                                    <i class="right fal fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>shoreTankMeasurement" class="nav-link">
                                        <i class="fal fa-database nav-icon"></i>
                                        <p>Shore Tank Measurement</p>
                                    </a>
                                </li>






                            </ul>

                        </li>

                    </ul>



                </li> -->



            </ul>
            <br>

        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>