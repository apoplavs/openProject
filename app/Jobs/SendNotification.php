<?php

namespace Toecyd\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Toecyd\Mail\NotificationMail;

/**
 * Class SendNotification
 * @package Toecyd\Jobs
 *          Черга для надсилання службових повідомлень (Notification) користувачам
 */
class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	// номер шаблону у views
    private $notification_id;
    
    // список отримувачів даного email, включаючи необхідні дані для автозаповнення шаблону
    private $recipients;
	
	// тема листа
    private $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $notification_id, array $recipients, string $subject = '') {
    	$this->notification_id = $notification_id;
    	$this->recipients = $recipients;
		$this->subject = $subject;
        //
    }

    /**
	 * Надсилання листів всім отримувачам з масиву recipients
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
    	
    	foreach($this->recipients as $recipient) {
			Mail::to($recipient['email'])
				->send(new NotificationMail($this->notification_id, $this->subject, $recipient));
		}
		
    }
}
