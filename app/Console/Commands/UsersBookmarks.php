<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Toecyd\CourtSession;
use Toecyd\UserBookmarkSession;

/**
 * Class UsersBookmarks
 * @package Toecyd\Console\Commands
 */
class UsersBookmarks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:users_bookmarks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматично оновити закладки користувачів змінивши id судових засіданнь що вже пройшли на майбутні';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle() {
    	// перевірка всіх закладок на засідання з датою < current_time всіх користувачів
        $past_session = UserBookmarkSession::getPastSession();
        
        foreach($past_session as $key => $session) {
        	// перевіряємо чи є майбутні засідання по цій справі
        	$future_session = CourtSession::getFutureSession($session['number']);
        	// якщо немає - пропускаємо
			if (empty($future_session)) {
				continue;
			}
			// якщо є оновлюємо закладку встановлюючи новий id засідання
			UserBookmarkSession::updateUserBookmark($session['id'], $future_session['id']);
		}
    }
}
