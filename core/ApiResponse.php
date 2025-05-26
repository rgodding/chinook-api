<?php
class ApiResponse {

    // Status List
    public const POS_STATUS = 'status';
    public const POS_DATA = 'data';
    public const POS_MESSAGE = 'message';

    // Different Statuses
    public const STATUS_SUCCESS = 'success';
    public const STATUS_SUCCESS_CREATED = 'success_created';
    public const STATUS_SUCCESS_NO_CONTENT = 'success_no_content';
    public const STATUS_SUCCESS_NOT_FOUND = 'not_found';
    public const STATUS_ERROR = 'error';
    public const STATUS_ERROR_CONFLICT = 'conflict';
    public const STATUS_ERROR_BAD_REQUEST = 'bad_request';

}


?>