<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookDownload;
use App\Models\BookReview;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    
    public function index()
    {
        //show all books
        //$books = Book::orderBy('title', 'asc')->get();
        $books= Book::with('authors','editorial','category','bookDownload')->get();

        return $this->getResponse200($books);

    }
    public function store(Request $request)
    { DB::beginTransaction();
        try {
            $isbn = preg_replace('/\s+/', '\u0020', $request->isbn); //Remove blank spaces from ISBN
            $existIsbn = Book::where("isbn", $isbn)->exists(); //Check if a registered book exists (duplicate ISBN)
            if (!$existIsbn) { //ISBN not registered
                $book = new Book();
                $book->isbn = $isbn;
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = date('y-m-d h:i:s'); //Temporarily assign the current date
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial["id"];
                $book->save();
                $bookDownload =new BookDownload();
                $bookDownload->book_id=$book->id;
                $bookDownload->save();
                foreach ($request->authors as $item) { //Associate authors to book (N:M relationship)
                    $book->authors()->attach($item);
                }
                DB::commit();
                return $this->getResponse201('book', 'created', $book);
            } else {
                return $this->getResponse500(['The isbn field must be unique']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([]);
        }
    }


    public function delete(Request $request,$id)
    
    {
        DB::beginTransaction();
        $existBook = Book::where("id", $id)->exists();
        try {
            if(!$existBook){
                return $this->getResponse404();

            }else{
                $book = Book::find($id);
                $book->bookDownload()->delete();
                foreach($book->authors as $item){
                    $book->authors()->detach($item->id);
                }
                
                $book->delete();
                DB::commit();
                return $this->getResponseDelete200($book);
                
               
            }
           
        
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([]);
        }
        
    }

    public function show(Request $request,$id)
    {
        $existBook = Book::where("id", $id)->exists();
        try {
            if(!$existBook){
                return $this->getResponse404();

            }else{
                
                $book= Book::with('authors','editorial','category','bookDownload')->get() ->where('id',$id);
                 return $this->getResponse200($book);

            }
           
        
        } catch (Exception $e) {
            return $this->getResponse500([]);
        }
        
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            
            $book = Book::find($id);
            
            if ($book==true) { 
                $isbn = trim($request->isbn);
                $isbnOwner = Book::where("isbn",$isbn)->first();
                if(!$isbnOwner ||$isbnOwner->id==$book->id){
                $book->isbn =$isbn;
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = date('y-m-d h:i:s'); //Temporarily assign the current date
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial["id"];
                $book->update();
                //delete
                foreach($book->authors as $item){
                    $book->authors()->detach($item->id);
                }
                //add new authors
                foreach ($request->authors as $item) { 
                    $book->authors()->attach($item);
                }
                DB::commit();
                return $this->getResponse201('book', 'update', $book);

                }else{

                    $response["message"]="ISBN dubplicated";
                }
            } else {
                return $this->getResponse404();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e->getMessage()]);
        }
    }

    public function addBookReview(Request $request)
    { 

   
                    DB::beginTransaction();
                    try {
                        $user= User::find(auth()->user()->id);

                    if(isset($user->id)){
                        $bookReview = new BookReview();
                        
                        $bookReview->comment = $request->comment;
                        $bookReview->book_id = $request->book["id"];
                        $bookReview->user_id = auth()->user()->id;

                        $bookReview->save();
                    
                        
                        DB::commit();
                        return $this->getResponse201('bookReview', 'created', $bookReview);
                       

                    }else{
                        return $this->getResponse404();

                    }
                
                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->getResponse500();
                    }
   
    }

    public function updateBookReview(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $user= User::find(auth()->user()->id);

        if(isset($user->id)){
            $bookReview = BookReview::find($id);
            
            if ($bookReview) { 
                
                    if($bookReview->user_id==$user->id){
                        $bookReview->comment=$request->comment;
                        $bookReview->edited=True;
        
                        $bookReview->update();
                        
                        DB::commit();
                        return $this->getResponse201('bookReview', 'update', $bookReview);
                    }else{
                        return $this->getResponse403();
                    }
             
                
 
                 }else{
 
                     return $this->getResponse404();
                    
                 }

            
            
           

        }else{
            return $this->getResponse404();

        }
    
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([]);
        }


}
}
