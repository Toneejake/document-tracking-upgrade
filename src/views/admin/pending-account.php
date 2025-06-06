<?php require 'template/top-template.php'; ?>



<?php
    try {
        //code...
        $get_user_data = "
            SELECT *
            FROM tbl_login_account
            JOIN tbl_userinformation ON tbl_login_account.id = tbl_userinformation.id
            WHERE tbl_login_account.status = 'pending'
        ";
        $stmt = $pdo->prepare($get_user_data);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $th) {
        echo "Error: " . $th->getMessage();
    }

?>




<style>
    :root {
    --primary-color: #069734;
    --lighter-primary-color: #07b940;
    --white-color: #FFFFFF;
    --black-color: #181818;
    --bold: 600;
    --transition: all 0.5s ease;
    --box-shadow: 0 0.1rem 0.8rem rgba(0, 0, 0, 0.2);
    }
    ::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background-color: #009933; 
        border-radius: 6px;
    }
    .table-container{
        padding: 2.5rem;
        background-color: #fff;
        box-shadow: var(--box-shadow);
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid var(--primary-color) !important;
        border-radius: 10px;
        padding: 5px;
        background-color: transparent;
        color: inherit;
        margin-left: 3px;
        
    }
    .dataTables_wrapper .dataTables_filter input:active {
        border: 1px solid var(--primary-color) !important;
        border-radius: 10px;
        padding: 5px;
    }
    #example_wrapper{
        overflow-x: scroll;
    }
    .main-content{
        position: relative;
        background-color: white;
        top: 0;
        max-height: 90vh;
        overflow-y: scroll;
        left: 90px;
        transition: var(--transition);
        width: calc(100% - 90px);
        padding: 1rem;

    }
</style>




<div class="table-container">
<div class="d-flex justify-content-end align-items-end mb-3">
        <!-- <a href="add-new-users.php?type=guest" class="btn btn-success d-flex align-items-center" style="gap: 10px" ><i class='bx bx-plus-circle' style="font-size: 18px"></i> User</a> -->
        <div class="dropdown">
            <i class='bx bx-dots-vertical-rounded' style="font-size: 40px;" id="dropdownIcon" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownIcon">
                <li><a class="dropdown-item" href="guest-archived.php"><i class='bx bx-show'></i> Declined registration</a></li>
                <!-- <li><a class="dropdown-item" href="#"><i class='bx bx-edit-alt'></i> Option 2</a></li>
                <li><button class="dropdown-item" onclick="customFunction()"><i class='bx bx-lock'></i> Option 3</button></li>
                <li><button class="dropdown-item" onclick="customFunction2()"><i class='bx bxs-trash'></i> Option 4</button></li> -->
            </ul>
        </div>
</div>
<table id="example" class="hover" style="width:100%">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Full Name</th>

                <th>Contact number</th>

                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row) { ?>
                <tr>
                    <td><?php echo $row['username'] ?></td>
                    <td><?php echo $row['role'] ?></td>
                    <td><?php echo $row['firstname'] ?> <?php echo $row['lastname'] ?></td>
                    <td><?php echo $row['contact'] ?></td>
            
                    <td><?php echo strlen($row['email']) > 10 ? substr($row['email'], 0, 10) . '...' : $row['email']; ?></td>
                    <td>
                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"><i class='bx bx-list-ul'></i></button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="user-details.php?id=<?php echo $row['id'] ?>"><i class='bx bx-show'></i> View Details</a></li>
                            <li><button class="dropdown-item" data-id="<?php echo $row['id'] ?>" onclick="approve(this)"><i class='bx bx-check' ></i> Approve</button></li>
                            <li><button class="dropdown-item" data-id="<?php echo $row['id'] ?>" onclick="decline(this)" ><i class='bx bx-x'></i>
                                    Decline</button></li>
                        </ul>
                    </div>
                    </td>
                </tr>
            
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Full Name</th>
                <th>Contact number</th>
    
                <th>Email</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
</div>



<?php require 'template/bottom-template.php'; ?>

<script>
    function decline(button){
        var userId = button.dataset.id;
        Swal.fire({
        title: "Decline Registration?",
        text: "This will decline the registration of the user and notify via email.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, decline it!"
        }).then((result) => {
        if (result.isConfirmed) {
            $('.loader-container').fadeIn();
            $.ajax({
                url: "../../controller/crud-users-controller.php",
                type: "POST",
                data: "action=decline_account&id="+userId,
                success:function(response){

                    setTimeout(function() {

                    $('.loader-container').fadeOut();
                    }, 500);
                
                    if(response.status === "failed"){
                        Swal.fire({
                            title: 'Something went wrong!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }else if(response.status === "error"){
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    }
                    else if(response.status === "success"){
                        Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();

                            }
                        });
                        }
                },
                error: function(xhr, status, error) {
                    // Handle the error here
                    var errorMessage = 'An error occurred while processing your request.';
                    if (xhr.statusText) {
                        errorMessage += ' ' + xhr.statusText;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage + '<br><br>' + JSON.stringify(xhr, null, 2), // Include the entire error object for debugging
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Check if the user clicked the "OK" button
                        if (result.isConfirmed) {
                            // Reload the page
                            location.reload();
                        }
                    });
                }
            });
        }
        });
    }
    function approve(button){
        var userId = button.dataset.id;
        Swal.fire({
        title: "Approve Registration?",
        text: "This will approve the registration of the user and notify via email.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, approve it!"
        }).then((result) => {
        if (result.isConfirmed) {
            $('.loader-container').fadeIn();
            $.ajax({
                url: "../../controller/crud-users-controller.php",
                type: "POST",
                data: "action=approve_account&id="+userId,
                success:function(response){

                    setTimeout(function() {

                    $('.loader-container').fadeOut();
                    }, 500);
                
                    if(response.status === "failed"){
                        Swal.fire({
                            title: 'Something went wrong!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }else if(response.status === "error"){
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    }
                    else if(response.status === "success"){
                        Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();

                            }
                        });
                        }
                },
                error: function(xhr, status, error) {
                    // Handle the error here
                    var errorMessage = 'An error occurred while processing your request.';
                    if (xhr.statusText) {
                        errorMessage += ' ' + xhr.statusText;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage + '<br><br>' + JSON.stringify(xhr, null, 2), // Include the entire error object for debugging
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Check if the user clicked the "OK" button
                        if (result.isConfirmed) {
                            // Reload the page
                            location.reload();
                        }
                    });
                }
            });
        }
        });
    }
</script>


<script>
    function archiveAccount(button){
        var userId = button.dataset.id;
        Swal.fire({
        title: "Archive account?",
        text: "This will lose the access of the user to the system.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, archive it!"
        }).then((result) => {
        if (result.isConfirmed) {
            $('.loader-container').fadeIn();
            $.ajax({
                url: "../../controller/crud-users-controller.php",
                type: "POST",
                data: "action=archive_account&id="+userId,
                success:function(response){

                    setTimeout(function() {

                    $('.loader-container').fadeOut();
                    }, 500);
                
                    if(response.status === "failed"){
                        Swal.fire({
                            title: 'Something went wrong!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }else if(response.status === "error"){
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    }
                    else if(response.status === "success"){
                        Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();

                            }
                        });
                        }
                },
                error: function(xhr, status, error) {
                    // Handle the error here
                    var errorMessage = 'An error occurred while processing your request.';
                    if (xhr.statusText) {
                        errorMessage += ' ' + xhr.statusText;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage + '<br><br>' + JSON.stringify(xhr, null, 2), // Include the entire error object for debugging
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Check if the user clicked the "OK" button
                        if (result.isConfirmed) {
                            // Reload the page
                            location.reload();
                        }
                    });
                }
            });
        }
        });
    }
</script>



