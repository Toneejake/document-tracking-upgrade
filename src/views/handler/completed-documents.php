<?php require 'template/top-template.php'; ?>

<?php 
$user_id = $_SESSION['userid'];
$office_session = $_SESSION['office'];
    try {
        //code...
        $docu_query = "SELECT * FROM tbl_uploaded_document where sender = :office and completed = 'complete'";
        $stmt = $pdo->prepare($docu_query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $docu_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (\Throwable $th) {
        //throw $th;
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
    .form-control{
        border: 2px solid #009933;
        border-radius: 10px;
    }

</style>

<!-- <div class="container">
    <form autocomplete="off">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="name">Document Type</label>
                <input type="text" class="form-control" id="name" placeholder="Enter your name">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="office">Date From</label>
                <input type="date" class="form-control" id="office" placeholder="Enter your office">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="from">Date To</label>
                <input type="date" class="form-control" id="from" placeholder="Enter sender's name">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
    </form>
</div> -->


<div class="table-container">
<style>
         @media (min-width: 992px) {
            .w-lg-25 {
                width: 10% !important;
            }
        }
    </style>
<div class="d-flex mb-3 justify-content-end align-items-end">
    <p class="mb-2 mr-3">Filter by date</p>
    <input type="text" class="form-control mr-3 w-lg-25 w-100" id="min" name="min" placeholder="Start date">
    <input type="text" class="form-control w-lg-25 w-100" id="max" name="max" placeholder="End date">
    <p class="ml-2" onclick="refreshPage()" style="cursor: pointer"><i class='bx bx-reset' style="font-size: 30px;"></i></p>
</div>
<script>
    function refreshPage(){
        window.location.reload();
    }
</script>
<table id="example" class="hover" style="width:100%">
        <thead>
            <tr>
                <th>QR Code</th>
                <th>Doc Code</th>
                <th>Document Type</th>
                <th>Document Source</th>
                <th>Receiving Unit</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        
        <?php foreach($docu_details as $detail){ ?>
            <tr>
            <td><img src="<?php echo $env_basePath; ?>assets/qr-codes/<?php echo $detail['qr_filename']; ?>" alt="QR Code" style="height: 80px"></td>
                <td><?php echo $detail['document_code'] ?></td>
                <td><?php echo $detail['document_type'] ?></td>
                <td><?php echo $detail['data_source'] ?></td>
                <td><?php echo $detail['receiver'] ?></td>
                <td><?php echo $detail['status'] ?></td>
                <td>
                    <a href="<?php echo $env_basePath; ?>views/track-document.php?code=<?php echo $detail['document_code']; ?>" class="btn btn-dark"><i class='bx bx-show'></i></a>
                </td>
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
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
</div>



<?php require 'template/bottom-template.php'; ?>