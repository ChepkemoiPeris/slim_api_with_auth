<?php
namespace App\Models;
use \Illuminate\Database\Eloquent\Model;

class Guest extends Model{
protected $table = "guests";
protected $fillable = ["full_name","email","comment"];
}