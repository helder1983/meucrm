<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageMenu extends Model
{
    use HasFactory;

    public $table = "pages_menus";
	public $fillable = [
		'action_id', 'menu_id'
	];
	public $searchable = [
		'action_id', 'menu_id'
	];

	public $timestamps = false;

    /**
	 * Relacionamentos
	 */
	public function menu() {
		return $this->hasOne(Menu::class, 'menu_id');
	}

    public function action() {
		return $this->hasOne(Action::class, 'action_id');
	}
}
