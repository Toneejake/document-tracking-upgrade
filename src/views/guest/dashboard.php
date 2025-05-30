<?php require 'template/top-template.php'; ?>
<?php 
    // Count all documents submitted by the current guest user
    $user_id = $_SESSION['userid'];
    $countAllUploadedDocuments = "SELECT COUNT(*) as all_documents from tbl_uploaded_document where sender_id = :user_id AND status != 'Document pulled'"; 
    $stmt = $pdo->prepare($countAllUploadedDocuments);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $allDocuCount = $result['all_documents'];

    // Count pending documents
    $countPendingDocuments = "SELECT COUNT(*) as pending_documents from tbl_uploaded_document where sender_id = :user_id AND status = 'pending'"; 
    $stmt = $pdo->prepare($countPendingDocuments);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pendingDocuCount = $result['pending_documents'];

    // Count completed documents
    $countCompletedDocuments = "SELECT COUNT(*) as complete_documents from tbl_uploaded_document where sender_id = :user_id AND (status like '%Document confirm by%' or status like '%Completed at%')"; 
    $stmt = $pdo->prepare($countCompletedDocuments);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $completedDocuCount = $result['complete_documents'];

    // Get recent documents
    $trackDocument = "SELECT * from tbl_uploaded_document where sender_id = :user_id AND status != 'pulled' ORDER BY updated_at DESC LIMIT 10";
    $stmt = $pdo->prepare($trackDocument);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    .table-container {
        padding: 2.5rem;
        background-color: #fff;
        box-shadow: var(--box-shadow);
        margin: 1rem;
    }
    .main-content {
        position: relative;
        background-color: white;
        top: 0;
        max-height: 90vh;
        overflow-y: scroll;
        padding: 1rem;
        margin-left: 90px; /* Add margin instead of left positioning */
        width: calc(100% - 90px);
    }
    .stats-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }
    .stats-number {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-color);
    }
    .row {
        margin-right: 0;
        margin-left: 0;
    }
</style>

<div class="row my-3">
    <div class="col-md-4">
        <div class="stats-card">
            <h5>Total Documents</h5>
            <div class="stats-number"><?php echo $allDocuCount; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <h5>Pending Documents</h5>
            <div class="stats-number"><?php echo $pendingDocuCount; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <h5>Completed Documents</h5>
            <div class="stats-number"><?php echo $completedDocuCount; ?></div>
        </div>
    </div>
</div>

<div class="table-container">
    <h4>Recent Documents</h4>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tracking Code</th>
                    <th>Document Type</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($results as $result): ?>
                <tr>
                    <td><?php echo $result['document_type']; ?></td>
                    <td><?php echo $result['subject']; ?></td>
                    <td><?php echo $result['status']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($result['updated_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>