<div class="row">
     <div class="col-lg-12">
        <h1 class="page-header">Welcome to the <?php echo $_SESSION['ACCOUNT_ID'] ? $_SESSION['ACCOUNT_TYPE'] : 'Admin'; ?> Panel</h1>
      </div>
      <!-- /.col-lg-12 -->
</div>

<!-- Integration System Buttons -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Integrated Systems</h3>
            </div>
            <div class="panel-body">
                <p>Access other integrated campus management systems below:</p>
                
                <div class="row" style="margin-top: 20px;">
                    <!-- Admission System Button - Updated icon -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-graduation-cap text-primary"></i>
                                </div>
                                <h4 style="margin-top: 0;">Admission</h4>
                                <p class="text-muted">Manage student admissions and enrollment details.</p>
                                <a href="#" class="btn btn-primary btn-block">
                                    <i class="fa fa-link"></i> Admission
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Event Management System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-calendar text-success"></i>
                                </div>
                                <h4 style="margin-top: 0;">Event Management</h4>
                                <p class="text-muted">Manage school events and schedules.</p>
                                <a href="#" class="btn btn-success btn-block">
                                    <i class="fa fa-link"></i> Event
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-users text-info"></i>
                                </div>
                                <h4 style="margin-top: 0;">Faculty</h4>
                                <p class="text-muted">Manage faculty and teacher records.</p>
                                <a href="#" class="btn btn-info btn-block">
                                    <i class="fa fa-link"></i> Faculty
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- MIS System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-database text-warning"></i>
                                </div>
                                <h4 style="margin-top: 0;">MIS</h4>
                                <p class="text-muted">Manage Management Information System (MIS) data.</p>
                                <a href="#" class="btn btn-warning btn-block">
                                    <i class="fa fa-link"></i> MIS
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Registrar System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-graduation-cap text-danger"></i>
                                </div>
                                <h4 style="margin-top: 0;">Registrar</h4>
                                <p class="text-muted">Manage academic records and registrations.</p>
                                <a href="#" class="btn btn-danger btn-block">
                                    <i class="fa fa-link"></i> Registrar
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Cashier System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-money text-success"></i>
                                </div>
                                <h4 style="margin-top: 0;">Cashier</h4>
                                <p class="text-muted">Manage tuition fees and payments.</p>
                                <a href="#" class="btn btn-success btn-block">
                                    <i class="fa fa-link"></i> Cashier
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Prefect System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-gavel text-primary"></i>
                                </div>
                                <h4 style="margin-top: 0;">Prefect</h4>
                                <p class="text-muted">Manage student behavior and discipline.</p>
                                <a href="#" class="btn btn-primary btn-block">
                                    <i class="fa fa-link"></i> Prefect
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Clinic System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-medkit text-danger"></i>
                                </div>
                                <h4 style="margin-top: 0;">Clinic</h4>
                                <p class="text-muted">Manage student health and medical records.</p>
                                <a href="#" class="btn btn-danger btn-block">
                                    <i class="fa fa-link"></i> Clinic
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Financial Assistance System Button - Updated icon -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-dollar text-info"></i>
                                </div>
                                <h4 style="margin-top: 0;">Financial Assistance</h4>
                                <p class="text-muted">Manage scholarships and financial aid.</p>
                                <a href="#" class="btn btn-info btn-block">
                                    <i class="fa fa-link"></i> Financial
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Library System Button -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                                <div style="font-size: 40px; margin-bottom: 10px;">
                                    <i class="fa fa-book text-warning"></i>
                                </div>
                                <h4 style="margin-top: 0;">Library</h4>
                                <p class="text-muted">Manage books, borrowing, and library records.</p>
                                <a href="#" class="btn btn-warning btn-block">
                                    <i class="fa fa-link"></i> Library
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>