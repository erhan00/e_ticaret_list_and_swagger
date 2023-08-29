<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musteriler_iletisim extends Model
{
    use HasFactory;

    protected $table = 'presentcommunication';
    protected $primaryKey = 'ID';


    // belongsTo() ilişkisi, bir veritabanı tablosunun başka bir tabloya ait olduğu durumları modellemek için kullanılır ve bu ilişkiyi kullanarak ilişkili verilere kolayca erişebilirsiniz.

    public function present()
    {
        return $this->belongsTo(Musteriler::class,'PRESENTID');
    }
}
