// Real-time notification system

let lastNotificationCount = 0;
let lastNotificationId = 0;
let notificationCheckInterval = 30000; // Check every 30 seconds
let notificationSound = new Audio('../assets/sounds/notification.mp3'); // Add a sound file to your assets

// Function to check for new notifications
function checkForNewNotifications() {
    const userId = document.querySelector('#openModalBtn').getAttribute('data-id');
    if (!userId) return;
    
    $.ajax({
        url: "../../controller/notification-controller.php",
        type: "POST",
        data: {
            action: "check_new_notifications",
            userid: userId,
            last_notification_id: lastNotificationId
        },
        success: function(response) {
            if (response.status === "success") {
                // Update the last notification ID
                if (response.last_notification_id) {
                    lastNotificationId = response.last_notification_id;
                }
                
                // If there are new notifications
                if (response.new_notifications > 0) {
                    // Update the notification count badge
                    const notificationBadge = document.querySelector('#openModalBtn .badge');
                    const currentCount = parseInt(response.total_count);
                    
                    // Only play sound and show alert if count increased
                    if (currentCount > lastNotificationCount) {
                        // Play notification sound
                        notificationSound.play();
                        
                        // Show a toast notification
                        Swal.fire({
                            title: 'New Notification',
                            text: response.latest_notification,
                            icon: 'info',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }
                    
                    // Update the badge
                    if (notificationBadge) {
                        notificationBadge.textContent = currentCount;
                    } else {
                        // Create badge if it doesn't exist
                        const badge = document.createElement('span');
                        badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        badge.textContent = currentCount;
                        document.querySelector('#openModalBtn').appendChild(badge);
                    }
                    
                    lastNotificationCount = currentCount;
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error checking notifications:", error);
        }
    });
}

// Start checking for notifications when the document is ready
$(document).ready(function() {
    // Initialize the last notification count
    const notificationBadge = document.querySelector('#openModalBtn .badge');
    if (notificationBadge) {
        lastNotificationCount = parseInt(notificationBadge.textContent);
    }
    
    // Get the initial last notification ID
    const userId = document.querySelector('#openModalBtn').getAttribute('data-id');
    if (userId) {
        $.ajax({
            url: "../../controller/notification-controller.php",
            type: "POST",
            data: {
                action: "get_last_notification_id",
                userid: userId
            },
            success: function(response) {
                if (response.status === "success" && response.last_notification_id) {
                    lastNotificationId = response.last_notification_id;
                }
                
                // Start the interval after getting the initial ID
                setInterval(checkForNewNotifications, notificationCheckInterval);
            }
        });
    }
});