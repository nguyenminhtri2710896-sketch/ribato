<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $code;
    public $markdown;

    public function __construct($email, $code, $markdown)
    {
        $this->email = $email;
        $this->code = $code;
        $this->markdown = $markdown;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('KHÔI PHỤC MẬT KHẨU')
            ->markdown($this->markdown);
    }
}
