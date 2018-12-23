<?php

namespace Toecyd\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Toecyd\Judge;
use Toecyd\JudgeStatus;
use Toecyd\Mail\NotificationMail;
use Toecyd\UserBookmarkJudge;

/**
 * Class SendNotification
 * @package Toecyd\Jobs
 *          Черга для надсилання повідомлення про зміну статусу судді користувачам, які його відстежують
 */
class SendNotification1 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	// id судді
	private $judge_id;
	
	// старий статус судді
    private $old_status;
    
    // новий статус судді
    private $new_status;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $judge_id, int $old_status, int $new_status) {
		$this->judge_id = $judge_id;
    	$this->old_status = $old_status;
    	$this->new_status = $new_status;
        //
    }

    /**
	 * Надсилання листів всім отримувачам з масиву recipients
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
		$recipients = UserBookmarkJudge::getRecipientsN1($this->judge_id);
		$judge = Judge::getFullNameById($this->judge_id);
		
		// отримуємо рядкові значення для статусів
		$this->old_status = JudgeStatus::find($this->old_status);
		$this->new_status = JudgeStatus::find($this->new_status);
		
    	foreach($recipients as $recipient) {
			Mail::to($recipient['email'])
				->send(new NotificationMail(1, 'Змінився статус судді', [
					'name'  => $recipient->name,
					'judge' => $judge->full_name,
					'old_status' => $this->old_status->title,
					'new_status' => $this->new_status->title
					]));
		}
		
    }
}
