<?php

namespace App\Http\Controllers;

use File;
use App\Models\Vsvideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Carga la página de inicio del objeto
        $vs_videos = VsVideo :: where ('active', '=', 1) -> get() ;
        $cont = VsVideo::count();
        $videos = $this->cargarDT($vs_videos);
        return view(view:'videos.index')-> with('videos', $videos)->with('cont', $cont);

    }

public function cargarDT($consulta)
    {
        $videos = [];

        foreach ($consulta as $key => $value){

            $ruta = "eliminar".$value['id'];
            $eliminar = route('delete-video', $value['id']);

            $actualizar =  route('videos.edit', $value['id']);
            $detalle =  route('videos.show', $value['id']);
            $acciones = '
                <div class="btn-acciones">
                    <div class="btn-circle">
                        <a href="'.$actualizar. '" role="button" class="btn btn-success" title="actualizar">
                            <i class="far fa-edit"></i>
                        </a>
                        <a href="' . $detalle . '" role="button" class="btn btn-primary" title="reproducir">
                            <i class="far fa-play"></i>
                        </a>
                         <a href="#'.$actualizar.'" role="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#'.$ruta.'">
                            <i class="far fa-trash-alt"></i>
                        </a>

                    </div>
                </div>
<!-- Modal -->
            <div class="modal fade" id="'.$ruta.'" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">¿Seguro que deseas eliminar este video?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-primary">
                        <small>
                            '.$value['id'].', '.$value['title'].'                 </small>
                      </p>
</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                      <a href="'.$eliminar.'" type="button" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

            ';
$videos[$key] = array(
                $acciones,
                $value['id'],
                $value['title'],
                $value['description'],
                $value['image'],
                $value['video_path'],
                $value['name'],
                $value['email']

            );

        }

        return $videos;
    }


    /**m run dev

     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // mostrar el formulario de captura
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //guarda un nuevo registro apartir de un formulario de nuevo registro
        //validacion del formulario
        $validateData = $this->validate($request, [
            'title' => 'required|min:5',
            'description' => 'required',
            'video' => 'mimes:mp4'
        ]);
        $video = new Video();
        $user = Auth::user();
        $video->user_id = $user->id;
        $video->title = $request->input(key:'title');
        $video->description = $request->input(key:'description');
        //Subida de la miniatura
        $image = $request->file('image');
        if ($image) {
            $image_path = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_path, \File::get($image));
            $video->image = $image_path;
        }
        //Subida del video
        $video_file = $request->file('video');
        if ($video_file) {
            $video_path = time() . $video_file->getClientOriginalName();
            \Storage::disk('videos')->put($video_path, \File::get($video_file));
            $video->video_path = $video_path;
        }

        $video->save();
        return redirect()->route(route:'home')->with(array(
            'message' => 'El video se ha subido correctamente'
        ));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //muestra un registro solamente
        $video = Vsvideo::find($id);
        return view(view: 'videos.show')->with('video',$video);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abri el formulario de edicion
        $user = Auth::user();
        $video = Video::find($id);
        if ($user && $video->user_id == $user->id) {
            return view('videos.edit', array('video' => $video));
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //guarda la modificacion de una edicion
        $validateData = $this->validate($request, [
            'title' => 'required|min:5',
            'description' => 'required',
            'video' => 'mimes:mp4'
        ]);
        $video =Video:: find($id);
        $video->title = $request->input(key: 'title');
        $video->description = $request->input(key: 'description');
        //Subida de la miniatura
        $image = $request->file('image');
        if ($image) {
            $image_path = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_path, \File::get($image));
            $video->image = $image_path;
        }
        //Subida del video
        $video_file = $request->file('video');
        if ($video_file) {
            $video_path = time() . $video_file->getClientOriginalName();
            \Storage::disk('videos')->put($video_path, \File::get($video_file));
            $video->video_path = $video_path;
        }

        $video->update();
        return redirect()->route(route: 'videos.index')->with(array(
            'message' => 'El video se ha subido correctamente'
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Borrado
    }

    public function delete_video($video_id)
    {
        $video = Video::find($video_id);
        if ($video) {
            $video->active = 0;
            $video->update();
            return redirect()->route('videos.index')->with(array(
                "message" => "El video se ha eliminado correctamente"
            ));
        } else {
            return redirect()->route('videos.index')->with(array(
                "message" => "El video que trata de eliminar no existe"
            ));
        }
    }

    public function getImage($filename)
    {
        $file = \Storage::disk('images')->get($filename);
        return new Response($file, 200);
    }



}
