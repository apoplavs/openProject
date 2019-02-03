<?php

namespace Toecyd\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AccountMail
 * @package Toecyd\Mail
 *          Відповідає за системні повідомлення
 *          підтвердження email, скидання паролю,
 *          оновлення пошти і т.п
 */
class AccountMail extends Mailable
{
    use Queueable, SerializesModels;
	
    // назва view, яку потрібно підключити
	private $view_name;
	
	// тема листа
	public $subject;
	
	// дані для заповнення листа інформацією
	private $data;
	
	
	/**
	 * Create a new message instance.
	 *
	 * @param string $view_name
	 * @param string $subject
	 * @param array  $data
	 */
	public function __construct(string $view_name = '', string $subject = '', array $data) {
		$this->view_name = $view_name;
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
			->view('mail.account.'.$this->view_name)
			->with(['data' => $this->data]);
	}
}
