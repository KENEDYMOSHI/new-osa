<div class="active tab-pane" id="timeline">
    <!-- The timeline -->
    <div class="timeline timeline-inverse">
        <!-- timeline time label -->
        <?php foreach ($tasks as $task) : ?>


        <div class="time-label">
            <span class="bg-success">
                <?= dateFormatter($task['created_at']) ?>
            </span>
        </div>
        <!-- /.timeline-label -->
        <!-- timeline item -->
        <div>
            <i class="far fa-check-circle bg-cyan"></i>

            <div class="timeline-item">
                <!-- <span class="time"><i class="far fa-clock"></i> 12:05</span> -->

                <h3 class="timeline-header"> <b> <?= $task['activity'] ?></b>
                </h3>

                <div class="timeline-body">
                    <p><?= $task['description'] ?></p>
                    <ul class="list-group">
                        <li class="list-group-item ">Region: <b><?= $task['region'] ?></b>
                        </li>
                        <li class="list-group-item ">District:
                            <b><?= $task['district'] ?></b>
                        </li>
                        <li class="list-group-item ">Group Name: <b><?= $task['the_group']  ?></b></li>





                    </ul>


                </div>

                <div class="timeline-footer">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <a href="<?= base_url() ?>/confirmTask/<?= $task['id'] ?>"
                                class="btn <?= $task['confirmation'] == 1 ? " btn-success" : "btn-danger" ?>"><?= $task['confirmation'] == 1 ? "Confirmed" : "Not Confirmed" ?></a>
                            <!-- <input type="text" id=task> -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ================================ -->
        <!-- END timeline item -->

        <!-- timeline time label -->

        <!-- /.timeline-label -->
        <!-- timeline item -->



        <!-- END timeline item -->

    </div>
</div>