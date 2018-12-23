<?php

namespace Toecyd\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class NotificationMail
 * @package Toecyd\Mail
 *          Клас для створення об'єкту листа повідомлень
 *          з необхідними параметрами
 */
class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    
    // номер шаблону у views
    private $id;
    
    // тема листа
    public $subject;
	
	// дані для заповнення листа інформацією
	private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $id = 1, string $subject = '', array $data) {
        $this->id = $id;
		$this->subject = $subject;
		$this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->from(env('MAIL_USERNAME', 'toe.cyd@gmail.com'), env('APP_NAME', 'ТОЕсуд'))
			->subject($this->subject)
			->view('mail.notifications.notification'.$this->id)
			->with(['data' => $this->data]);
    }
}
