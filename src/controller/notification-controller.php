<?php 
    session_start();
        
    require '../connection.php';
    require '../model/tbl-conversation-message-model.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';
    require '../connection.php';
    require '../model/tbl-login-account-model.php';
    require '../model/tbl-userinformation-model.php';
    require '../model/tbl-office-name-model.php';
    require '../model/tbl-notification-model.php';
    
    $accountModel = new AccountModel($pdo);
    $userInforamtion = new UserInformation($pdo);
    $officeInfo = new OfficeNames($pdo);
    $notificationModel = new NotificationModel($pdo);

    $userid = $_SESSION['userid'];

    $conversationModel = new ConversationModel($pdo);

    // Mark notifications as read
    if(isset($_POST['action']) && $_POST['action'] == 'mark_as_read'){
        try {
            header('Content-Type: application/json');
            $user_id = $_POST['userid'];
            $notificationModel->markAsReadbyUser($user_id);

            echo json_encode(['status' => 'success']);
            exit();
        } catch (\Throwable $th) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $th->getMessage()]);
            exit();
        }
    }

    // Check for new notifications
    if(isset($_POST['action']) && $_POST['action'] == 'check_new_notifications'){
        try {
            header('Content-Type: application/json');
            $user_id = $_POST['userid'];
            $last_notification_id = isset($_POST['last_notification_id']) ? $_POST['last_notification_id'] : 0;
            
            // Get count of unread notifications
            $notifCountQuery = "SELECT COUNT(*) from tbl_notification where user_id = :user_id and status = 'unread'";
            $stmt = $pdo->prepare($notifCountQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $notificationCount = $stmt->fetchColumn();
            
            // Get new notifications since last check
            $newNotificationsQuery = "SELECT COUNT(*) from tbl_notification where user_id = :user_id and id > :last_id";
            $stmt = $pdo->prepare($newNotificationsQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':last_id', $last_notification_id);
            $stmt->execute();
            $newNotifications = $stmt->fetchColumn();
            
            // Get the latest notification content
            $latestNotificationQuery = "SELECT content, id from tbl_notification where user_id = :user_id ORDER BY id DESC LIMIT 1";
            $stmt = $pdo->prepare($latestNotificationQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $latestNotification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $response = [
                'status' => 'success',
                'total_count' => $notificationCount,
                'new_notifications' => $newNotifications,
                'latest_notification' => $latestNotification ? $latestNotification['content'] : '',
                'last_notification_id' => $latestNotification ? $latestNotification['id'] : $last_notification_id
            ];
            
            echo json_encode($response);
            exit();
        } catch (\Throwable $th) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $th->getMessage()]);
            exit();
        }
    }

    // Get last notification ID
    if(isset($_POST['action']) && $_POST['action'] == 'get_last_notification_id'){
        try {
            header('Content-Type: application/json');
            $user_id = $_POST['userid'];
            
            // Get the latest notification ID
            $latestNotificationQuery = "SELECT id from tbl_notification where user_id = :user_id ORDER BY id DESC LIMIT 1";
            $stmt = $pdo->prepare($latestNotificationQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $latestNotification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'status' => 'success',
                'last_notification_id' => $latestNotification ? $latestNotification['id'] : 0
            ]);
            exit();
        } catch (\Throwable $th) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $th->getMessage()]);
            exit();
        }
    }
?>