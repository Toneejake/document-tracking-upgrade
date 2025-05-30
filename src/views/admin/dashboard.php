<?php require 'template/top-template.php'; ?>
<?php 
    $countAllUploadedDocuments = "SELECT COUNT(*) as all_documents from tbl_uploaded_document where status != 'Document pulled'";
    $stmt = $pdo->prepare($countAllUploadedDocuments);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $allDocuCount = $result['all_documents'];

    $countConfirmDisconfirmDocument = "SELECT COUNT(*) as complete_documents from tbl_uploaded_document where status like '%Document confirm by%' or status like '%Completed at%'";
    $stmt = $pdo->prepare($countConfirmDisconfirmDocument);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $completedDocuCount = $result['complete_documents'];

    $countOffices = "SELECT COUNT(*) as no_offices from tbl_login_account where role = 'handler' and status = 'active'";
    $stmt = $pdo->prepare($countOffices);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $no_offices = $result['no_offices'];

    
    $countExternal = "SELECT COUNT(*) as no_external from tbl_login_account where role = 'guest' and status = 'active'";
    $stmt = $pdo->prepare($countExternal);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $no_external = $result['no_external'];

    $doctypeQuery = "SELECT * FROM tbl_document_type";

    $statement = $pdo->prepare($doctypeQuery);
    $statement->execute();
    $doctypes = $statement->fetchAll(PDO::FETCH_ASSOC);

    $trackDocument = "SELECT * from tbl_uploaded_document where status != 'pulled' and status != 'pending' ORDER BY updated_at DESC";
    $stmt = $pdo->prepare($trackDocument);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
    $get_user_count = "
        SELECT COUNT(*)
        FROM tbl_login_account
        JOIN tbl_userinformation ON tbl_login_account.id = tbl_userinformation.id
        WHERE tbl_login_account.status = 'pending'
    ";
    $stmt = $pdo->prepare($get_user_count);
    $stmt->execute();

    $count = $stmt->fetchColumn(); // Fetching the count directly
    
    
   
    
?>


<style>
    :root {
    --primary-color: #069734;
    --lighter-primary-color: #07b940;
    --white-color: #FFFFFF;
    --black-color: #181818;
    --bold: 600;
    --transition: all 0.5s ease;
    --box-shadow: 0 0.1rem 0.4rem rgba(0, 0, 0, 0.2);
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
    .form-control{
        border: 2px solid #009933;
        border-radius: 10px;
    }
</style>

<div class="row my-3">
        
    
        <div class="col-md-4">
            <a href="guest.php" style="text-decoration: none; color: black">
                <div class="card text-center card-info">
                    <div class="card-block">
                        <h4 class="card-title mt-3">No. of guest accounts</h4>
                        <h2><i class="fa fa-user fa-2x"></i></h2>
                    </div>
                    <div class="row p-2 ">
                        <div class="col-12">
                            <div class="card card-block text-success rounded-0 ">
                                <h3><?php echo $no_external; ?></h3>
                                <span class="small text-uppercase">users</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            
        </div>
        <div class="col-md-4">
        <a href="offices.php" style="text-decoration: none; color: black">
            <div class="card text-center card-info">
                <div class="card-block">
                    <h4 class="card-title mt-3">No. of document handler account</h4>
                    <h2><i class="fa fa-users fa-2x"></i></h2>
                </div>
                <div class="row p-2 ">
                    <div class="col-12">
                        <div class="card card-block text-success rounded-0 ">
                            <h3><?php echo $no_offices; ?></h3>
                            <span class="small text-uppercase">users</span>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-4">
        <a href="pending-account.php" style="text-decoration: none; color: black">
            <div class="card text-center card-info">
                <div class="card-block">
                    <h4 class="card-title mt-3">Incoming Registration</h4>
                    <h2><i class="fa fa-file fa-2x"></i></h2>
                </div>
                <div class="row p-2 ">
                    <div class="col-12">
                        <div class="card card-block text-success rounded-0 ">
                            <h3><?php echo $count; ?></h3>
                            <span class="small text-uppercase">items</span>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>





<!-- 
    <div class="table-container mt-5">
    <h3>Generate Reports</h3>
    <br>
        <form id="generate_reports_form" action="export-pdf-template.php" method="get" autocomplete="off" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
            <div class="form-group">
                <label for="doc_type">Document Type:</label>
                <select name="doc_type" class="form-control"  id="doc_type">
                    <option value="Select" selected>Select</option>
                   
                    <option value="complete" >Complete Documents</option>
                    <option value="decline" >Incomplete Documents</option>
                    <option value="ongoing" >Ongoing Documents</option>
                </select>
            </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sender">Date From:</label>
                    <input type="date" class="form-control" placeholder="Date From" id="from" name="from">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label for="receiver">Date To:</label>
                <input type="date" class="form-control" placeholder="Date To" id="dateto" name="dateto">
            </div>
            </div>

        </div>
        <div class="d-flex justify-content-end align-items-end" style="gap: 20px">
                <button type="reset" class="btn btn-danger">Clear</button>
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>
    </div> -->

    <!-- <div class="table-container mt-5" style="overflow-x: auto;">
    <h3>Track Documents</h3>
    <br>
<table id="example" class="hover" style="width:100%">
        <thead>
            <tr>
                <th>QR Code</th>
                <th>Doc Code</th>
                <th>Document Type</th>
                <th>Document Source</th>
                <th>Sender</th>
                <th>Previous Office</th>
                <th>Current Office</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
       
            <?php foreach ($results as $row) { ?>
                <tr style="<?php
                if($row['completed']  == 'no'){
                    $receiveTimestamp = strtotime($row['updated_at']);
                    $currentTimestamp = time();
                    $threeDaysInSeconds = 3 * 24 * 60 * 60; // 3 days in seconds
                    $fiveDaysInSeconds = 5 * 24 * 60 * 60;  // 5 days in seconds

                    if ($currentTimestamp - $receiveTimestamp > $fiveDaysInSeconds) {
                        echo 'background-color: #FFC0C0;';  // Set background color for more than 5 days
                    } elseif ($currentTimestamp - $receiveTimestamp > $threeDaysInSeconds) {
                        echo 'background-color: #FFEC94;';  // Set background color for more than 3 days
                    }
                }
                
                ?>">
                <td><img src="<?php echo $env_basePath; ?>assets/qr-codes/<?php echo $row['qr_filename']; ?>" alt="QR Code" style="height: 80px"></td>
                    <td><?php echo $row['document_code'] ?></td>
                    <td><?php echo $row['document_type'] ?></td>
                    <td><?php echo $row['data_source'] ?></td>
                    <td><?php echo $row['sender'] ?></td>
                   <td><?php echo strlen($row['status']) > 50 ? substr($row['status'], 0, 50) . '...' : $row['status']; ?></td> 
                    <td><?php echo $row['prev_office'] ?></td>
                    <td><?php echo $row['cur_office'] ?></td>
                    <td><a href="<?php echo $env_basePath; ?>views/track-document.php?code=<?php echo $row['document_code']; ?>" class="btn btn-dark"><i class='bx bx-show'></i></a></td>
                </tr>
            <?php } ?>

        </tbody>
        <tfoot>
            <tr>
                <th>QR Code</th>
                <th>Doc Code</th>
                <th>Document Type</th>
                <th>Document Source</th>
                <th>Receiving Unit</th>
                <th>Previous Office</th>
                <th>Current Office</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
</div> -->

        
<?php require 'template/bottom-template.php'; ?>

<script>

    var fromDateInput = document.getElementById('from');
    var toDateInput = document.getElementById('dateto');


    fromDateInput.addEventListener('input', handleDateChange);
    toDateInput.addEventListener('input', handleDateChange);

    function handleDateChange() {
        var currentDate = new Date();

        var fromDate = new Date(fromDateInput.value);
        var toDate = new Date(toDateInput.value);

        if (toDate < fromDate) {
            toDateInput.value = fromDateInput.value;
        }


        if (toDate > currentDate) {
            toDateInput.valueAsDate = currentDate;
        }
        if (fromDate > currentDate) {
            fromDateInput.valueAsDate = currentDate;
        }
    }
</script>
