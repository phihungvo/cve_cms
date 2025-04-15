<?php
return [
    'title' => 'Delete Media',
    'message' => 'Are you sure you want to permanently delete the media ":name"? This action cannot be undone.',
    'delete-success' => 'Media deleted successfully',
    'delete-success-file-only' => 'File deleted from MinIO, but no record found in database',
    'delete-error-not-found' => 'File not found on MinIO and no record in database',
    'delete-error' => 'Failed to delete media',
    'restore-success' => 'Media restored successfully.',
    'restore-error' => 'Failed to restore media.',
    'force-delete-success' => 'Media permanently deleted successfully.',
    'force-delete-error' => 'Failed to permanently delete media.',
    'no-permission' => 'You do not have permission to perform this action.',
];