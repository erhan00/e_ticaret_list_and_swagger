<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Musteriler;
use App\Models\Musteriler_iletisim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;// ifadesiyle DB facadesını kullanarak veritabanı sorguları yapabilir ve hataları yakalayabilirsiniz. catch bloğunda hata işleme kodlarını ekleyebilirsiniz.
use Illuminate\Database\QueryException;// veritabanı sorgusu yapılırken bir hata oluşursa, QueryException sınıfı tarafından yakalanır ve $e değişkeni üzerinden hata mesajına erişilir.
use Illuminate\Support\Facades\Validator;// Validator sınıfı kullanılarak gelen VERİLER belirtilen kurallara göre doğrulanıyor.
use Illuminate\Database\Eloquent\ModelNotFoundException; // ModelNotFoundException, Eloquent ORM'un bir parçasıdır ve Eloquent modelinde belirtilen bir kaydın bulunamadığında atılacak özel bir istisna sınıfıdır. Bu istisna, veritabanında belirtilen koşullara uyan bir kayıt bulunamadığında tetiklenir.
use Illuminate\Validation\ValidationException;// ValidationException sınıfı doğrulama sırasında oluşan hataları yönetirken,Validator sınıfı veri doğrulama işlemlerini gerçekleştirmek için kullanılır.


/**
 * @OA\Info(title="Swagger Dökümantasyon", version="0.1")
 */

class liste extends Controller
{
    //@OA\Parameter anotasyonları eklenerek isteğe bağlı olarak kullanılan parametreler belgelenir. 
  /**
 * @OA\Get(
 *     path="/customers",
 *     summary="belirtilen path ile ilgili tablonun verileri",
 *     tags={"present tablo"},
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         description="db'den çekilecek veri sınırı",
 *     @OA\Schema( type="integer"  )
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         required=false,
 *         description="verileri almadan önce atlanacak öğe sayısı",
 *     @OA\Schema( type="integer"  )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Not acceptable",
 *     ),
 * )
 */
    public function index(Request $request){
      try{
        //$musteri_kayitlar = DB::table('present')->get();
       // $musteri_kayitlar = Musteriler::all();
       // URL'indeki ? sembolü, URL sorgusunun başlangıcını belirtir. 
       $limit = $request->input('limit',10);
       $offset = $request->input('offset',0);
       $varsayilan_limit_degeri = 500;
     // if($limit = is_numeric($limit) && $limit <= 500 ? $limit : $varsayilan_limit_degeri){
       //  $uyari = "500'den_buyuk_limit_degeri_girilmesi_durumunda_default_olarak_500_kayıt_dondurulur.";
      //}
      if (is_numeric($limit) && $limit <= 500) {
          // Limit değeri geçerliyse, aynı şekilde devam eder
      } else {
          $limit = $varsayilan_limit_degeri; // Geçerli değilse, varsayılan limit değeri atanır
      }


        $query = Musteriler::query(); //query() metodu, Eloquent modelini temel sorgu yapısına dönüştürür. Bu, sorgu işlemlerini daha fazla zincirleme yapısına uyarlamak ve sorguyu daha fazla özelleştirmek için kullanılır. Örneğin, where, orderBy, join gibi sorgu yöntemlerini ardışık olarak kullanarak sorgunuzu inşa edebilirsiniz. all() ise belırtılen modelın tum  verılerını ceker.
       

        if ($request->has('PRESENTCODE')) {  // ifadesi ile isteğin içinde PRESENTCODE adında bir parametrenin olup olmadığı kontrol edilir.
            $query->where('PRESENTCODE', $request->input('PRESENTCODE')); // Kullanıcı herhangi farklı bir PRESENTCODE değeri girerse, o değere göre sonuçlar değişir. 
          }
      

        if ($request->has('PRESENTTITLE')) {
            $query->where('PRESENTTITLE', 'LIKE', '%' . $request->input('PRESENTTITLE') . '%'); //Veri Kapsamı: Büyük veri kümelerinde, tam eşleşmeleri aramak pratik olmayabilir. LIKE operatörü ve % sembolleri, belirli bir terimin farklı varyasyonlarını içeren kayıtları da bulmanıza yardımcı olur. Bu, veritabanınızda esnek arama yetenekleri sunmanızı sağlar.
          }
        

        if ($request->has('PRESENTAUTHORNAME')) {
            $query->where('PRESENTAUTHORNAME', 'LIKE', '%' . $request->input('PRESENTAUTHORNAME') . '%'); 
           
        }

       // $results = $query->get();       
        $results = $query->skip($offset)->take($limit)->get(); 
       
        $row_count = $results->count();
         $total__row_count = Musteriler::query()->count();


        if ($results->isEmpty()) { //$results adında bir sonuç kümesi veya koleksiyon varsa, isEmpty() metodu ile bu sonuç kümesinin boş olup olmadığı kontrol edilir. Eğer sonuç kümesi boşsa, isEmpty() true değerini döndürür 'if blogu' ve ilgili kod bloğu çalışır. Doluysa false değerini döndürür ve else kod bloğu çalışır.
          return response()->json(['Success' => false, 'Message' => 'kayit_mevcut_degil'], 404);
      } 
      else{
      // Sonuçlar dolu ise 200 döndür
      return response()->json(['Success' => true, 'Data' => $results,'row_count'=>$row_count,'total_row_count'=>$total__row_count], 200);
      }
      }
      catch (ModelNotFoundException $e) {
        return response()->json(['Success' => false, 'Message' => 'kaynak_bulunamadi.'], 404);// 404 durum kodu mu ??? 
      }
      catch(QueryException $e){
        return response()->json(['Success' => false, 'Message' =>$e->getMessage()],500);
      }

    }


// in: Bu parametre, parametrenin nerede bulunduğunu belirtir. "query" değeri, parametrenin URL sorgu parametresi olarak geçtiğini ifade eder
 /**
 * @OA\Get(
 *     path="/connected_customer/{id}",
 *     summary="belirtilen path ile ilişkili tabloların verileri",
 *     tags={"present ilişkili tablo"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="id parametresi gerekli",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Not acceptable",
 *     ),
 * )
 */
    public function show(int $id){

         $musteri_iletisim = Musteriler_iletisim::find($id); 
        // iletişim  oluşturan kullanıcı
         if ($musteri_iletisim) {
          $present00 = $musteri_iletisim->present;   // MUsteri_iletisim içeriisndekı present fonksıyonunu belirttik yanı,model de present fonksıyonu ıcerısndekı belongTo() ilişkisini burada temsil  eden fonksıyon 'present', 2 tablo arasındakı ilişkiyı, PRESENTID 'si olan kullanıcıyı getırır
          return response()->json(['Data' => $musteri_iletisim],200);
         }
          return response()->json(['Success'=>false,'Message' => 'kayit_bulunamadi'], 404);

    }
}
