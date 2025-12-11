<div class="card card-primary ">
    <div class="card-header">
        <h3 class="card-title">Tasks And Groups</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>

        </div>
    </div>
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activities</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Collapse">
                        <i class="fas fa-minus"></i></button>

                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped projects">
                    <thead>
                        <tr>

                            <th>
                                Activity
                            </th>

                            <th>
                                Group Name
                            </th>
                            <th>
                                Confirmation
                            </th>


                        </tr>
                    </thead>
                    <tbody>

                        <?php



                        function group_by($key, $activities)
                        {
                            $result = array();

                            foreach ($activities as $val) {
                                $result[$val[$key]][] = $val;
                                if (array_key_exists($key, $val)) {
                                } else {
                                    $result[""][] = $val;
                                }
                            }

                            return $result;
                        }

                        // $byGroup = group_by("the_group", $activities);
                        $byGroup = group_by("activity", $activities);


                        ?>

                        <?php foreach ($byGroup as $activity) : ?>


                        <tr>
                            <?php foreach ($activity as $item) : ?>
                            <?php endforeach; ?>
                            <td><?= $item['activity'] ?>
                                <br>
                                <span>Created: <?= dateFormatter($item['created_at']) ?></span>
                            </td>


                            <td><?= $item['the_group'] ?></td>
                            <?php if ($item['confirmation'] == 0) : ?>
                            <td><span class="badge badge-pill badge-danger">Not Confirmed</span></td>
                            <?php else : ?>
                            <td><span class="badge badge-pill badge-success">Confirmed</span></td>
                            <?php endif; ?>


                        </tr>

                        <?php endforeach; ?>





                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.card-body -->
</div>