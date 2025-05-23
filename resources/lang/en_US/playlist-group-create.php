<?php return [
    'meta-title' => 'Playlist>Groups',
    'enterprise' => 'Enterprise',
    'select-enterprise' => '-- select Enterprise --',
    'name' => 'Name',
    'description' => 'Description',
    'success' => 'Playlist Group created successfully.',
    'error' => [
        'unauthorized' => 'You are not authorized to create a playlist group: :message',
        'database' => 'A database error occurred: :message',
        'validation-error' => 'Validation failed: :message',
        'unknown-error' => 'An unexpected error occurred: :message',
        'enterprise-not-found' => 'The selected enterprise does not exist.',
        'name-required' => 'The name field is required.',
        'name-too-long' => 'The name must not exceed 100 characters.',
        'description-too-long' => 'The description must not exceed 255 characters.',
        'invalid-enterprise-id' => 'The enterprise ID must be a valid integer.',
    ],
];
