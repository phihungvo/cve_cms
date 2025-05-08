<?php return [
    'meta-title' => 'Device>Groups>Update',
    'btn-delete' => 'Delete',
    'btn-force-delete' => 'Force Delete',
    'btn-restore' => 'Restore',
    'btn-save' => 'Save',
    'btn-cancel' => 'Cancel',
    'error' => [
        'not-found' => 'Device group not found.',
        'name-required' => 'The name field is required.',
        'name-string' => 'The name must be a string.',
        'name-max' => 'The name may not be greater than 100 characters.',
        'description-string' => 'The description must be a string.',
        'description-max' => 'The description may not be greater than 255 characters.',
        'enterprise-id-integer' => 'The enterprise ID must be an integer.',
        'enterprise-id-exists' => 'The selected enterprise ID is invalid.',
    ],

    'modal' => [
        'delete' => [
            'title' => 'Delete Device Group',
            'message' => 'Are you sure you want to delete this device group? This action cannot be undone.',
        ],
        'force-delete' => [
            'title' => 'Force Delete Device Group',
            'message' => 'Are you sure you want to permanently delete this device group? This action cannot be undone.',
        ],
        'restore' => [
            'title' => 'Restore Device Group',
            'message' => 'Are you sure you want to restore this device group?',
        ],
    ],

    'update' => [
        'success' => 'Device group updated successfully.',
        'error' => 'An error occurred while updating the device group: :message',
        'database-error' => 'Database error during device group update: :message',
        'type-error' => 'Type error occurred during device group update: :message',
        'validation-error' => 'Validation error during device group update: :message',
    ],
    'delete' => [
        'success' => 'Device group deleted successfully.',
        'error' => [
            'in-use' => 'Cannot delete this device group because it is still in use.',
            'query-error' => 'Cannot delete this device group due to a database query error: :message',
            'database-error' => 'Database error occurred during deletion: :message',
            'connection-error' => 'Database connection error occurred during deletion: :message',
            'unexpected-error' => 'Unexpected error occurred during deletion: :message',
        ],
    ],
    'force-delete' => [
        'success' => 'Device group permanently deleted successfully.',
        'error' => [
            'in-use' => 'Cannot force delete this device group because it is still in use.',
            'model-not-found' => 'Force delete failed: Model not found.',
            'query-error' => 'Force delete failed: Database query error: :message',
            'unexpected-error' => 'Force delete failed: Unexpected error: :message',
        ],
    ],
    'restore' => [
        'success' => 'Device group restored successfully.',
        'error' => [
            'model-not-found' => 'Restore failed: Model not found.',
            'query-error' => 'Restore failed: Database query error.',
            'connection-error' => 'Restore failed: Database connection error.',
            'not-authorized' => 'Restore failed: You are not authorized to perform this action.',
            'validation-failed' => 'Restore failed: Validation error occurred.',
            'unexpected-error' => 'Restore failed: Unexpected error.',
        ],
    ],
];

