<?php

return [
    // Index (Danh sách thông báo)
    'notification-index' => [
        'title' => 'Notifications',
        'filter' => 'Search notifications...',
        'meta-title' => 'Notification List',
    ],

    // Create (Tạo thông báo)
    'notification-create' => [
        'title' => 'Create Notification',
        'meta-title' => 'Create New Notification',
        'title-label' => 'Title',
        'title-placeholder' => 'Enter notification title',
        'content-label' => 'Content',
        'content-placeholder' => 'Enter notification content',
        'type-label' => 'Notification Type',
        'type-system' => 'System',
        'type-enterprise' => 'Enterprise',
        'enterprise-label' => 'Enterprise',
        'enterprise-none' => 'None',
        'target-group-label' => 'Target Group',
        'target-group-all' => 'All',
        'user-ids-label' => 'Specific Users',
        'user-ids-placeholder' => 'Select users',
        'submit' => 'Create',
        'cancel' => 'Cancel',
        'success' => 'Notification created successfully.',
        'failed' => 'Failed to create notification.',
        'no-permission' => 'You do not have permission to create notifications.',
        'owner-enterprise-mismatch' => 'You can only create notifications for your enterprise.',
        'owner-requires-enterprise' => 'Enterprise ID is required for owners.',
    ],

    // Update (Cập nhật thông báo)
    'notification-update' => [
        'title' => 'Update Notification',
        'meta-title' => 'Update Notification',
        'title-label' => 'Title',
        'title-placeholder' => 'Enter notification title',
        'content-label' => 'Content',
        'content-placeholder' => 'Enter notification content',
        'type-label' => 'Notification Type',
        'type-system' => 'System',
        'type-enterprise' => 'Enterprise',
        'target-group-label' => 'Target Group',
        'target-group-all' => 'All',
        'submit' => 'Update',
        'cancel' => 'Cancel',
        'success' => 'Notification updated successfully.',
        'failed' => 'Failed to update notification.',
        'invalid-notification' => 'Invalid notification.',
        'invalid-data' => 'Invalid data provided.',
        'invalid-target-group' => 'Invalid target group selected.',
        'unauthorized' => 'You do not have permission to update this notification.',
        'unauthorized-system' => 'Only root users can update system notifications.',
        'owner-enterprise-mismatch' => 'You can only update notifications for your enterprise.',
    ],

    // Delete (Xóa thông báo)
    'notification-delete' => [
        'title' => 'Delete Notification',
        'message' => 'Are you sure you want to delete the notification: :name?',
        'delete-success' => 'Notification deleted successfully.',
        'delete-error' => 'Failed to delete notification.',
        'delete-error-not-found' => 'Notification not found.',
        'force-delete-success' => 'Notification permanently deleted successfully.',
        'force-delete-error' => 'Failed to permanently delete notification.',
        'restore-success' => 'Notification restored successfully.',
        'restore-error' => 'Failed to restore notification.',
        'no-permission' => 'You do not have permission to perform this action.',
    ],

    // Read (Đánh dấu đã đọc)
    'notification-read' => [
        'success' => 'Notification marked as read successfully.',
        'failed' => 'Failed to mark notification as read.',
        'no-permission' => 'You do not have permission to mark this notification as read.',
        'already-read' => 'Notification is already marked as read.',
    ],

    // Show (Xem chi tiết thông báo)
    'notification-show' => [
        'title' => 'Notification Details',
        'meta-title' => 'Notification Details',
        'not-found' => 'Notification not found.',
        'no-permission' => 'You do not have permission to view this notification.',
        'failed' => 'Failed to load notification details.',
    ],

    // Các chuỗi chung
    'STT' => 'No.',
    'Title' => 'Title',
    'Content' => 'Content',
    'Type' => 'Type',
    'Enterprise' => 'Enterprise',
    'Target Group' => 'Target Group',
    'Sender' => 'Sender',
    'Created At' => 'Created At',
    'Read Status' => 'Read Status',
    'Actions' => 'Actions',
    'System' => 'System',
    'Read' => 'Read',
    'Unread' => 'Unread',
    'View Details' => 'View Details',
    'Back to List' => 'Back to List',
    'No notifications found' => 'No notifications found.',
];