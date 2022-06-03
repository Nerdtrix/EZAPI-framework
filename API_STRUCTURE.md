## Login API
- URL path: /authenticate/login
- method: post 

### body payload example
```json
{"usernameOrEmail": "required string", "password": "required string", "rememeberMe": "optional bool"}
```

### status 200 response
```json
{
    "result": {
        "id": 1,
        "roleId": 1,
        "statusId": 1,
        "isTwoFactorAuth": false,
        "fName": "first name",
        "lName": "last name",
        "email": "emailaddress@gmail.com",
        "username": "username",
        "locale": "en_US",
        "createdAt": "2022-04-26 17:12:54",
        "updatedAt": "2022-06-01 17:07:08",
        "deletedAt": null
    }
}
```

### status 400 response structure
```json
{
    "error": {
        "code": 400,
        "message": "invalid_username_or_password"
    }
}
```

### status 400 response messages
- Invalid request body
- Username_or_email_is_required
- password_is_required
- user_not_found
- invalid_username_or_password
- account_banned
- account_inactive
- unable_to_authenticate
- Maximun_devices_logged
- unable_to_create_OTP_session
- otp_validation_required

