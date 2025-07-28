
<?php $__env->startSection('content'); ?>
<div class="body mt-5">
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#add">Boyong</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover js-basic dataTable table-custom spacing5">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Kepala Kamar</th>
                    <th>Asrama</th>
                    <th>Alasan Boyong</th>
                    <th>Rencana</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $boyong; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->nis); ?></td>
                    <td><?php echo e($item->nama); ?></td>
                    <td><?php echo e($item->kelas); ?></td>
                    <td><?php echo e($item->kepkam->nama); ?></td>
                    <td><?php echo e($item->asrama->nama); ?></td>
                    <td><?php echo e($item->alasan->keterangan); ?></td>
                    <td><?php echo e($item->rencana->keterangan); ?></td>
                    <td><?php echo e(date('d-m-Y', strtotime($item->tanggal))); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Santri Boyong</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/boyong" method="post" class="form-group">
                    <?php echo csrf_field(); ?>
                    <div class="mt-2">
                        <label for="">NIS</label>
                        <input class="form-control" type="text" name="nis">
                    </div>
                    <div class="mt-2">
                        <label for="">Nama Santri</label>
                        <input class="form-control" type="text" name="nama">
                    </div>
                    <div class="mt-2">
                        <label for="">Kelas</label>
                        <input class="form-control" type="text" name="kelas">
                    </div>
                    <div class="mt-2">
                        <label for="">Kepala Kamar</label>
                        <select class="form-control" name="kepkam">
                            <?php $__currentLoopData = $kepkam; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->nis); ?>"><?php echo e($item->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Asrama</label>
                        <select class="form-control" name="asrama">
                            <?php $__currentLoopData = $asrama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Alasan Boyong</label>
                        <select class="form-control" name="alasan">
                            <?php $__currentLoopData = $alasan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->keterangan); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Rencana</label>
                        <select class="form-control" name="rencana">
                            <?php $__currentLoopData = $rencana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->keterangan); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Input</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\superapps\resources\views/kantor/boyong.blade.php ENDPATH**/ ?>