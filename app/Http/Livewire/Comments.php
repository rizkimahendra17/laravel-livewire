<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;


class Comments extends Component
{
    use WithPagination, WithFileUploads;
    
    public $newComment;
    public $image;
    public $ticketId;
    // public $data;

    protected $listeners = [
        'fileUpload' => 'handleFileUpload',
        'ticketSelected' => 'ticketSelected',
    ];


    public function ticketSelected($ticketId)
    {
        $this->ticketId = $ticketId;
    }


    public function handleFileUpload($imageData)
    {
        $this->image = $imageData;
    }

   
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, ['newComment'=>'required|max:225']);
    }

    public function addComment()
    {

        $this->validate(['newComment'=>'required|max:225']);

       $image =  $this->storeImage();

         // Begin Transaction
        \DB::beginTransaction();

        try{
            
            $createdComment =  Comment::create([
                'body' => $this->newComment,
                'user_id'=>1,
                'image' => $image,
                'support_ticket_id' => $this->ticketId
            ]);
            //kita simpan
            \DB::commit();
            
            session()->flash('message', 'Comment Berhasil Ditambahkan');
        }catch(Exception $e){
            \DB::rollback();
        }
       
        $this->resetForm();
    }

    public function storeImage()
    {
        if(!$this->image) 
        {
        return null;
        }

        $img = ImageManagerStatic::make($this->image)->encode('jpg');
        
        $name = Str::random(). '.jpg';

        Storage::disk('public')->put($name,$img);

        return $name;
    }


    public function resetForm()
    {
        $this->newComment = '';
        $this->image = '';
    }

    public function remove($commentid)
    {
        $comment = Comment::find($commentid);

        $comment->delete();
        Storage::disk('public')->delete($comment->image);
        // dd($comment);
        session()->flash('message', 'Comment Berhasil DiHapus');

    }

    public function render()
    {
         $comments = Comment::where('support_ticket_id',$this->ticketId)->latest()->paginate(2);
        // dd($comments);
        return view('livewire.comments',compact('comments'));
    }
}