<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class MasterDepartment extends Model
{
    protected $table = 'master_departments';
 
    protected $fillable = ['name'];
}
