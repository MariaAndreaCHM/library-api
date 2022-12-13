<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use App\Models\Book;


class AuthorController extends Controller
{
    public function index()
    {
       // $authors = Author::orderBy('first_surname', 'asc')->get();
        $authors= Author::with('books')->get();


        return $this->getResponse200($authors);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $author = new Author();
               
                $author->name = $request->name;
                $author->first_surname = $request->first_surname;
                $author->second_surname = $request->second_surname;
                
                $author->save();
                DB::commit();
                return $this->getResponse201('author', 'created', $author);
            
        } catch (Exception $e) {
            DB::rollBack();

            return $this->getResponse500([]);
        }
    }
    public function show(Request $request,$id)
    {
        $existAuthor = Author::where("id", $id)->exists();
        try {
            if(!$existAuthor){
                
                return $this->getResponse404();

            }else{
                $author= Author::with('books')->get() ->where('id',$id);



                 return $this->getResponse200($author);
                

            }
           
        
        } catch (Exception $e) {
            return $this->getResponse500([]);
        }
        

        
    }

    public function delete(Request $request,$id)
    {
        $existAuthor = Author::where("id", $id)->exists();
        DB::beginTransaction();
        try {
            if(!$existAuthor){
                return $this->getResponse404();

            }else{
                $author = Author::find($id);
               
                foreach($author->books as $item){
                    $author->books()->detach($item->id);
                }
                $author->delete();
                DB::commit();
                return $this->getResponseDelete200($author);
          
            }
           
        
        } catch (Exception $e) {
            DB::rollBack();

            return $this->getResponse500([]);
        }
        
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            
            $author = Author::find($id);
            
            if ($author) { 
                $author->name = $request->name;
                $author->first_surname = $request->first_surname;
                $author->second_surname = $request->second_surname;
                $author->update();
                DB::commit();
                return $this->getResponse201('author', 'update', $author);
            } else {
                return $this->getResponse404();
            }
        } catch (Exception $e) {
            DB::rollBack();

            return $this->getResponse500([]);
        }
    }
}
