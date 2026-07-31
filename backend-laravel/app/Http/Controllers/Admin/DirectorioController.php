<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectorioPersona;
use App\Imports\DirectorioImport;
use App\Exports\PlantillaDirectorioExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DirectorioController extends Controller
{
    public function index(Request $request)
    {
        $personas = DirectorioPersona::when($request->filled('buscar'), fn($q) =>
                $q->where('nombre', 'ilike', '%' . $request->buscar . '%')
                  ->orWhere('cedula', 'like', '%' . $request->buscar . '%')
            )
            ->when($request->filled('tipo'), fn($q) =>
                $q->where('tipo', $request->tipo)
            )
            ->orderBy('nombre')
            ->paginate($request->get('per_page', 20));

        return response()->json($personas);
    }

    public function buscar(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $personas = DirectorioPersona::where('nombre', 'ilike', '%' . $request->q . '%')
            ->orWhere('cedula', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'cedula', 'nombre', 'tipo', 'carrera', 'facultad', 'telefono', 'email']);

        return response()->json(['personas' => $personas]);
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new DirectorioImport();
        Excel::import($import, $request->file('archivo'));

        $errores = collect($import->failures())->map(fn($f) => [
            'fila'    => $f->row(),
            'campo'   => $f->attribute(),
            'errores' => $f->errors(),
        ]);

        return response()->json([
            'message'      => 'Importación completada',
            'importados'   => $import->importados,
            'actualizados' => $import->actualizados,
            'errores'      => $errores,
        ]);
    }

    public function plantilla()
    {
        return Excel::download(
            new PlantillaDirectorioExport(),
            'plantilla_directorio.xlsx'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula'   => 'required|digits:10|unique:directorio_personas,cedula',
            'nombre'   => 'required|string|max:255',
            'tipo'     => 'required|in:estudiante,docente,administrativo,externo',
            'carrera'  => 'nullable|string',
            'facultad' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email'    => 'nullable|email',
        ]);

        $persona = DirectorioPersona::create($request->all());

        return response()->json([
            'message' => 'Persona agregada al directorio',
            'persona' => $persona,
        ], 201);
    }

    public function destroy($id)
    {
        DirectorioPersona::findOrFail($id)->delete();
        return response()->json(['message' => 'Persona eliminada del directorio']);
    }
}