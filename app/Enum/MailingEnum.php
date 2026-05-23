<?php

namespace App\Enum;

enum MailingEnum: string
{
    // Type
    case SIGN_UP_OTP = 'sign_up_otp';

    case LOGIN_OTP = 'login_otp';

    case RESEND_CODE = 'resend_code';

    case RESET_OTP = 'reset_otp';

    case TWO_FA_OTP = 'two_fa_otp';

    case EMAIL_VERIFICATION = 'email_verification';

    case ACCOUNT_VERIFICATION = 'account_verification';

    case ORDER_STATUS_UPDATED = 'order_status_updated';

    case ADMIN_ACCOUNT = 'Admin new account';

    // Status
    case PENDING = 'pending';

    case SENT = 'sent';

    case FAILED = 'failed';
}
