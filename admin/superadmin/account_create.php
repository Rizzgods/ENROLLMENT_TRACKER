<?php

require_once ("database.php");
?>

<div class="container">
    <h1 class="text-center">Account Management</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Create New Account</strong></div>
        <div class="panel-body">
            <form id="accountForm" action="accr_be.php" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="account_id" id="account_id" value="">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="accountName">Full Name</label>
                            <input type="text" id="accountName" name="accountName" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="accountType">Account Type</label>
                            <select id="accountType" name="accountType" class="form-control" required>
                                <option value="">Select Account Type</option>
                                <option value="Administrator">Administrator</option>
                                <option value="Registrar">Registrar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employeeId">Employee ID</label>
                            <input type="text" id="employeeId" name="employeeId" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <p class="help-block">Leave blank to keep current password when editing.</p>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
                <button type="button" class="btn btn-default hidden" id="cancelBtn">Cancel</button>
            </form>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Admin Accounts</strong></div>
        <div class="panel-body">
            <input type="text" id="AdminSearch" class="form-control" placeholder="Search Admins by Name...">
            <table id="AdminTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>EMPID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($admin_accounts as $row): ?>
        <tr>
            <td><?php echo $row['ACCOUNT_ID']; ?></td>
            <td><?php echo $row['ACCOUNT_NAME']; ?></td>
            <td><?php echo $row['ACCOUNT_USERNAME']; ?></td>
            <td><?php echo $row['ACCOUNT_TYPE']; ?></td>
            <td><?php echo $row['EMPID']; ?></td>
            <td>
                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row['ACCOUNT_ID']; ?>">Edit</button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['ACCOUNT_ID']; ?>">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/acc_cr.js"></script>
