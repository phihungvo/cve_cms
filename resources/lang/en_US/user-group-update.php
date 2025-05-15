<?php return [
    'meta-title' => 'User>Groups>Update',
    'btn-delete' => 'Delete',
    'btn-force-delete' => 'Force Delete',
    'btn-restore' => 'Restore',
    'btn-save' => 'Save',
    'btn-cancel' => 'Cancel',

    'modal' => [
        'delete' => [
            'title' => 'Delete User Group',
            'message' => 'Are you sure you want to delete this user group? This action cannot be undone.',
        ],
        'force-delete' => [
            'title' => 'Force Delete User Group',
            'message' => 'Are you sure you want to permanently delete this user group? This action cannot be undone.',
        ],
        'restore' => [
            'title' => 'Restore User Group',
            'message' => 'Are you sure you want to restore this user group?',
        ],
    ],

    'update' => [
        'success' => 'User group updated successfully.',
        'error' => 'An error occurred while updating the user group: :message',
        'database-error' => 'Database error during user group update: :message',
        'type-error' => 'Type error occurred during user group update: :message',
        'validation-error' => 'Validation error during user group update: :message',
    ],
    'delete' => [
        'success' => 'User group deleted successfully.',
        'error' => [
            'in-use' => 'Cannot delete this user group because it is still in use.',
            'query' => 'Database query error occurred during deletion.',
            'connection' => 'Database connection error occurred during deletion.',
            'unexpected' => 'Failed to delete user group: :message',
        ],
    ],
    'force-delete' => [
        'success' => 'User group permanently deleted successfully.',
        'error' => [
            'in-use' => 'Cannot delete this user group because it is still in use.',
            'model-not-found' => 'Force delete failed: Model not found.',
            'query-error' => 'Force delete failed: Database query error.',
            'unexpected-error' => 'Force delete failed: Unexpected error.',
        ],
    ],
    'restore' => [
        'success' => 'User group restored successfully.',
        'error' => [
            'model-not-found' => 'User group not found or already deleted permanently.',
            'query-error' => 'Database error while restoring user group.',
            'connection-error' => 'Database connection error during restore.',
            'not-authorized' => 'Not authorized to restore this user group.',
            'validation-failed' => 'Validation failed during user group restore.',
            'unexpected-error' => 'Unexpected error during user group restore.',
        ],
    ],
];
