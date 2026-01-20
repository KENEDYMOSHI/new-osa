<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>/managerDashboard" class="brand-link">

        <span class="brand-text font-weight-bold ml-3"> WMA-MIS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <!-- entire link open -->
                <li class="nav-item ">
                    <a href="#" class="nav-link ">
                        <?php if ($profile->avatar): ?>
                            <img src="<?= $profile->avatar ?>" class="img-circle elevation-1" alt="User Image" width="28">
                        <?php else: ?>
                            <img src="<?= base_url() ?>/assets/images/avatar.png" class="img-circle elevation-1"
                                alt="User Image" width="28">
                        <?php endif; ?>
                        <p>
                            <?= $profile->first_name . ' ' . $profile->last_name ?>
                            <i class="right fal fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/changePassword" class="nav-link">
                                <i class="fal fa-key nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/managerProfile" class="nav-link">
                                <!-- <i class="fal fa-list-alt nav-icon"></i> -->
                                <i class="fal fa-user nav-icon"></i>
                                <p>My Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/logout" class="nav-link">
                                <!-- <i class="fal fa-list-alt nav-icon"></i> -->
                                <i class="fal fa-sign-out-alt nav-icon"></i>
                                <p>Log Out</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url() ?>/managerDashboard" class="nav-link ">
                        <i class="fal fa-tachometer-alt nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Dashboard

                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url() ?>/searchManager" class="nav-link ">
                        <i class="fal fa-search nav-icon"></i>
                        <p>
                            Search

                        </p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="<?= base_url() ?>/reportsManager" class="nav-link ">
                        <i class="fal fa-file-chart-pie nav-icon"></i>
                        <p>
                            Reports
                            <!-- <i class="right fal fa-angle-left"></i> -->
                        </p>
                    </a>

                </li>
                <li class="nav-item ">
                    <a href="<?= base_url() ?>/collectionTargetManager" class="nav-link ">
                        <i class="fal fa-bullseye-arrow nav-icon"></i>
                        <p>
                            Collection Target
                        </p>
                    </a>

                </li>
                <li class="nav-item ">
                    <a href="<?= base_url() ?>/analyticsManager" class="nav-link ">
                        <i class="fal fa-analytics nav-icon"></i>
                        <p>
                            Analytics
                        </p>
                    </a>

                </li>
                <li class="nav-item">
                    <a href="<?= base_url() ?>/assignTask" class="nav-link ">
                        <i class="fal fa-layer-plus nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Assign A Task

                        </p>
                    </a>


                </li>
                <li class="nav-item">
                    <a href="<?= base_url() ?>/viewTasks" class="nav-link ">
                        <i class="fal fa-tasks nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            View Tasks

                        </p>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a href="<?= base_url() ?>/dashboard" class="nav-link ">
                        <i class="fal fa-user-chart nav-icon"></i>

                        <p>
                            Progress

                        </p>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a href="#" class="nav-link ">
                        <i class="fal fa-money-check-alt nav-icon"></i>
                        <!-- <ion-icon class="nav-icon" name="speedometer-outline"></ion-icon> -->
                        <p>
                            Activities
                            <i class="right fal fa-angle-left"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/listAllScales" class="nav-link">
                                <i class="fal fa-balance-scale nav-icon"></i>
                                <p>Scales</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/listFuelPumps" class="nav-link">

                                <i class="fal fa-gas-pump nav-icon"></i>
                                <p>Fuel Pumps</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/listIndustrialPackages" class="nav-link">

                                <i class="fal fa-box-full nav-icon"></i>
                                <p>Pre Package</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/listVehicleTanks" class="nav-link">

                                <i class="fal fa-truck-container nav-icon"></i>
                                <p>Vehicle Tank Verification</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/listLorries" class="nav-link">

                                <i class="fal fa-truck-moving nav-icon"></i>
                                <p>Sand & Ballast Lorries</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/listBulkStorageTanks" class="nav-link">

                                <i class="fal fa-database nav-icon"></i>
                                <p>Bulk Storage Tanks</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/listFixedStorageTanks" class="nav-link">

                                <i class="fal fa-container-storage nav-icon"></i>
                                <p>Fixed Storage Tanks</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a href="<?= base_url() ?>/FlowMeterList" class="nav-link">

                                <i class="fal fa-bullseye nav-icon"></i>
                                <p>Flow Meter</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/WaterMeterList" class="nav-link">

                                <i class="fal fa-ring nav-icon"></i>
                                <p>Meter</p>
                            </a>
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
                                    <a href="<?= base_url() ?>/timeLog" class="nav-link">
                                        <i class="fal fa-clock nav-icon"></i>
                                        <p>Time Log</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/documents" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Documents</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/ullageBeforeDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Ullage Before Discharging</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/ullageAfterDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Ullage After Discharging</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a href="<?= base_url() ?>/petroleum" class="nav-link">
                                        <i class="fal fa-gas-pump nav-icon"></i>
                                        <p>Petroleum</p>
                                    </a>
                                </li> -->
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/certificateOfQuantity" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Certificate Of Quantity</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/noteOfFactBeforeDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Note Of Fact Before</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/noteOfFactAfterDischarging" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Note Of Fact After</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/dischargingSequence" class="nav-link">
                                        <i class="fal fa-file-alt nav-icon"></i>
                                        <p>Discharging Sequence</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/lineDisplacement" class="nav-link">
                                        <i class="fal fa-draw-circle nav-icon"></i>
                                        <p>Line Displacement</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/provisionalReport" class="nav-link">
                                        <i class="fal fa-file-chart-line nav-icon"></i>
                                        <p>Provisional Report</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/dischargeOrder" class="nav-link">
                                        <i class="fal fa-chart-network nav-icon"></i>
                                        <p>Discharge Order</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/pressureLog" class="nav-link">
                                        <i class="fal fa-clipboard-list nav-icon"></i>
                                        <p>Pressure Log</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a href="<?= base_url() ?>/edibleOil" class="nav-link">
                                        <i class="fal fa-burn nav-icon"></i>
                                        <p>Edible Oil</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url() ?>/LPG" class="nav-link">
                                        <i class="fal fa-gas-pump nav-icon"></i>
                                        <p>LPG</p>
                                    </a>
                                </li> -->





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
                                    <a href="<?= base_url() ?>/shoreTankMeasurement" class="nav-link">
                                        <i class="fal fa-database nav-icon"></i>
                                        <p>Shore Tank Measurement</p>
                                    </a>
                                </li>






                            </ul>

                        </li>

                    </ul>



                </li>

        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>