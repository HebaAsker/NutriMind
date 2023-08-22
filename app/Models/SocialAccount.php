<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory;

    protected $table = 'social_accounts';
    protected $guard = [];


    public function doctor(){
      return  $this->belongsTo(Doctor::class);
    }

    public function patient(){
      return  $this->belongsTo(Patient::class);
    }

}
