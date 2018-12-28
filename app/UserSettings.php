<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserSettings
 * @package Toecyd
 */
class UserSettings extends Model
{
	public $primaryKey = 'user';
	
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'email_notification_1', 'email_notification_2', 'email_notification_3',
		'email_notification_4', 'email_notification_5', 'email_notification_6'
	];
}
