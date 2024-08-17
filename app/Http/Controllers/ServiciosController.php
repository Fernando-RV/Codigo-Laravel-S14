<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Servicio;
use Illuminate\Http\Request;
use App\Events\ServicioSaved;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateServicioRequest;

class ServiciosController extends Controller
{
    public function index()
    {
        $servicios = Servicio::get();

        return view('servicios', compact('servicios'));
    }

    public function show($id){
        return view('show',[
            'servicio' => Servicio::find($id)
        ]);
    }

    
    public function create(){
        return view('create',[
            'servicio' => new Servicio,
            'categories' => Category::pluck('name', 'id'), //pluck significa extraer
        ]);
    }

    public function store(CreateServicioRequest $request){
        
        $servicio = new Servicio($request->validated());
        $servicio->image = $request->file('image')->store('images');
        $servicio->save();

        $image = Image::make(storage::get($servicio->image))
            ->widen(600) //Redimensionamos la imagen  a 600 px
            ->limitColors(255) //Limitamos el color a 255
            ->encode(); //Volvemos a codificar la nueva imagen

        //Sobreescribimos la misma imagen con la nueva imagen redimensionada
        Storage::put($servicio->image, (string) $image);
        //Creamos el disparador enviando como parámetro el servicio
        ServicioSaved::dispatch($servicio);

        return redirect()->route('servicios.index')->with('estado','El servicio fue creado correctamente');
    }

    public function edit(Servicio $servicio){
        return view('edit',[
            'servicio' => $servicio,
            'categories' => Category::pluck('name', 'id')
        ]);
    }
/*--->
    public function update(Servicio $id){
        $id->update([
            'titulo' => request('titulo'),
	        'descripcion' => request('descripcion')
        ]);

        return redirect()->route('servicios.show',$id);
    }
**/
    public function update(Servicio $servicio, CreateServicioRequest $request){

        if($request->hasFile('image') ){ //Si enviamos una Imagen
            Storage::delete($servicio->image); //LE PASAMOS LA UBICACIÓN DE LA IMAGEN
            $servicio->fill($request->validated() ); //Rellena todos los datos sin guardarlos
            $servicio->image = $request->file('image')->store('images'); //Le asignamos la imagen que sube
            $servicio->save(); //Finalmente guardamos en la Base de datos

            //Optimizar la Imagen que se ha guardado
            //primero creamos la instancia
            $image = Image::make(storage::get($servicio->image))
                ->widen(600) //Redimensionamos la imagen  a 600 px
                ->limitColors(255) //Limitamos el color a 255
                ->encode(); //Volvemos a codificar la nueva imagen

            //Sobreescribimos la misma imagen con la nueva imagen redimensionada
            Storage::put($servicio->image, (string) $image);
            //Creamos el disparador enviando como parámetro el servicio
            ServicioSaved::dispatch($servicio);

        } else{
            $servicio->update( array_filter($request->validated()) );
        }
        
	    return redirect()->route('servicios.show',$servicio)->with('estado', 'El servicio fue actualizado correctamente');
    }


    public function destroy(Servicio $servicio){
        
        Storage::delete($servicio->image); //LE PASAMOS LA UBICACIÓN DE LA IMAGEN

        $servicio->delete();
        
	    return redirect()->route('servicios.index')->with('estado','El servicio fue eliminado correctamente');
    }

    public function __construct(){
        //$this->middleware('auth')->only('create','edit');
        $this->middleware('auth')->except('index','show');

    }

}