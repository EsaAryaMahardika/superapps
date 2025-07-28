<!DOCTYPE html> 
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <link rel="icon" href="" type="image/x-icon">
        <title><?php echo e(strtoupper($user)); ?> - An-Nur II</title>
        <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap/css/bootstrap.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('vendor/animate-css/vivify.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/site.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')); ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
    </head>
    <body class="theme-light font-montserrat light_version">
        <div id="wrapper">
            <nav class="navbar top-navbar">
                <div class="container-fluid">
                    <div class="navbar-left">
                        <div class="navbar-btn">
                            <button type="button" class="btn-toggle-offcanvas"><i class="lnr lnr-menu fa fa-bars"></i></button>
                        </div>
                    </div>        
                    <div class="navbar-right">
                        <div id="navbar-menu">
                            <ul class="nav navbar-nav">
                                <li><a class="icon-menu" id="mode"><i class="fa fa-2x fa-sun" id="icon"></i></a></li>
                                <li><a href="/logout" class="icon-menu"><i class="fa fa-2x fa-power-off"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div id="left-sidebar" class="sidebar">
                <div class="navbar-brand">
                    <span>An-Nur II</span>
                    <button type="button" class="btn-toggle-offcanvas btn btn-sm float-right"><i class="lnr lnr-menu fa fa-chevron-circle-left"></i></button>
                </div>
                <div class="sidebar-scroll">
                    <div class="user-account">
                        <div class="dropdown">
                            <span>Selamat Datang</span>
                            
                        </div>
                    </div>  
                    <nav id="left-sidebar-nav" class="sidebar-nav">
                        <ul id="main-menu" class="metismenu">
                            <?php echo $__env->make('sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </ul>
                    </nav>     
                </div>
            </div>
            <div id="main-content">
                <?php if(Session::has('success')): ?>
                <div class="alert success-alert">
                    <p><?php echo e(Session::get('success')); ?></p>
                    <a class="close">&times;</a>
                </div>
                <?php elseif(Session::has('error-message')): ?>
                <div class="alert danger-alert">
                    <p><?php echo e(Session::get('error-message')); ?></p>
                    <a class="close">&times;</a>
                </div>
                <?php endif; ?>
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>
        <script>
            const BASE_URL = "<?php echo e(url('/')); ?>";
        </script>
        <script src="<?php echo e(asset('js/libscripts.bundle.js')); ?>"></script>    
        <script src="<?php echo e(asset('js/vendorscripts.bundle.js')); ?>"></script>    
        <script src="<?php echo e(asset('js/mainscripts.bundle.js')); ?>"></script>
        <script src="<?php echo e(asset('js/script.js')); ?>"></script>
        <script src="<?php echo e(asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
        <?php echo $__env->yieldContent('script'); ?>
    </body>
</html><?php /**PATH C:\laragon\www\superapps\resources\views/layout.blade.php ENDPATH**/ ?>