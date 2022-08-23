<?php
    namespace Services\Auth;

    enum ErrorMessage : string
    {
        const INVALID_INPUT = "invalid_username_or_password";
        const ACCOUNT_BANNED = "account_banned";
        const ACCOUNT_INACTIVE = "account_inactive";
        const ACCOUNT_BLOCKED = "account_blocked";
        const TOO_MANY_ATTEMPTS = "account_locked_for_too_may_attempts";
        const OTP_VALIDATION_REQUIRED = "otp_validation_required";
        const UNABLE_TO_CREATE_SESSION = "unable_to_create_otp_session";
        const UNABLE_TO_AUTHENTICATE = "unable_to_authenticate";
        const SOMETHING_WENT_WRONG = "something_went_wrong";
        const ACCOUNT_DELETED = "account_deleted";
    }
?>