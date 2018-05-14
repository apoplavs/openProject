<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
	public $timestamps = false;
	// The attributes that are mass assignable.
	protected $fillable = [
		'address', 'phone', 'email', 'site'
	];
}
