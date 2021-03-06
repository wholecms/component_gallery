<?php

namespace Components\Gallery\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Components\Gallery\Repositories\Gallery\GalleryRepository;
use Whole\Core\Logs\Facade\Logs;

class GalleriesController extends Controller
{
    protected $gallery;

    /**
     * @param GalleryRepository $gallery
     */
    public function __construct(GalleryRepository $gallery)
    {
        $this->gallery = $gallery;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $galleries = $this->gallery->all();
        return view('backend::galleries.index',compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('backend::galleries.create');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
		if ($this->gallery->saveData('create',$request->all()))
		{
			Flash::success('Başarıyla Kaydedildi');
			Logs::add('process',"Galeri Başarıyla Eklendi. \n");
			return redirect()->route('admin.gallery.index');
		}else
		{
			Flash::error('Bir Hata Meydana Geldi ve Galeri');
			Logs::add('errors',"Galeri Eklerken Hata Meydana Geldi! \n");
			return redirect()->back();
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
		//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $gallery = $this->gallery->find($id);
        return view('backend::galleries.edit',compact('gallery'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if ($this->gallery->saveData('update',$request->all(),$id))
        {
            Logs::add('process',"Galeri Başarıyla Düzenlendi \nID:{$id}");
            Flash::success('Başarıyla Düzenlendi');
            return redirect()->route('admin.gallery.index');
        }
        else
        {
            Logs::add('errors',"Galeri Düzenlerken Hata Meydana Geldi \nID:{$id}");
            Flash::error('Bir Hata Meydana Geldi ve Düzenlenemedi');
            return redirect()->back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $message = $this->gallery->delete($id) ?
            ['success','Başarıyla Silindi'] :
            ['error','Bir Hata Meydana Geldi ve Silinemedi'];
        if($message[0]=="success")
        {
            Logs::add('process',"Galeri Başarıyla Silindi \nID:{$id}");
        }else
        {
            Logs::add('errors',"Galeri Silinemedi \nID:{$id}");
        }
        Flash::$message[0]($message[1]);
        return redirect()->route('admin.gallery.index');
    }

}
