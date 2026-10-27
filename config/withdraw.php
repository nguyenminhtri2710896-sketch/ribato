<?php

return [
    // Hạn mức rút tối đa / ngày cho các tài khoản KHÔNG nằm trong whitelist (user_withdraw_whitelists)
    'non_whitelist_limit_in_day' => (int) env('WITHDRAW_NON_WHITELIST_LIMIT_IN_DAY', 100000000),
];
