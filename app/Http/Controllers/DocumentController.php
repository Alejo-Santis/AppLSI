<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use SweetAlert2\Laravel\Swal;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for an employee.
     */
    public function documents(Employee $employee)
    {
        $documents = $employee->documents()
            ->with('uploader')
            ->orderBy('upload_date', 'desc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'document_type' => $doc->document_type,
                    'document_type_label' => Document::getDocumentTypes()[$doc->document_type] ?? $doc->document_type,
                    'file_name' => $doc->file_name,
                    'file_extension' => $doc->file_extension,
                    'file_size_formatted' => $doc->file_size_formatted,
                    'upload_date' => $doc->upload_date->format('Y-m-d'),
                    'upload_date_human' => $doc->upload_date->diffForHumans(),
                    'uploader' => $doc->uploader ? $doc->uploader->name : 'Sistema',
                    'is_pdf' => $doc->isPdf(),
                    'is_image' => $doc->isImage(),
                ];
            });

        return response()->json($documents);
    }

    /**
     * Store a newly created document.
     */
    public function storeDocument(Request $request, Employee $employee)
    {
        try {

            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'document_type' => ['required', 'in:' . implode(',', array_keys(Document::getDocumentTypes()))],
                'file' => [
                    'required',
                    'file',
                    'max:' . (Document::getMaxFileSize() / 1024), // en KB
                    'mimes:' . implode(',', Document::getAllowedExtensions())
                ],
            ], [
                'title.required' => 'El título es obligatorio',
                'document_type.required' => 'El tipo de documento es obligatorio',
                'document_type.in' => 'El tipo de documento no es válido',
                'file.required' => 'Debe seleccionar un archivo',
                'file.file' => 'El archivo no es válido',
                'file.max' => 'El archivo no puede superar los 10MB',
                'file.mimes' => 'Formato de archivo no permitido. Formatos válidos: ' . implode(', ', Document::getAllowedExtensions()),
            ]);

            // Subir archivo
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('employees/documents/' . $employee->id, $fileName, 'public');

            // Crear documento
            $document = Document::create([
                'employee_id' => $employee->id,
                'title' => $validated['title'],
                'document_type' => $validated['document_type'],
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
                'upload_date' => now(),
            ]);

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Empleado creado exitosamente',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Crear!',
                'text' => 'No es posible crear el documento' . $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    /**
     * Download a document.
     */
    public function downloadDocument(Employee $employee, Document $document)
    {
        // Verificar que el documento pertenece al empleado
        if ($document->employee_id !== $employee->id) {
            abort(403, 'No autorizado');
        }

        // Verificar que el archivo existe
        if (!Storage::disk('public')->exists($document->file_path)) {
            Swal::error([
                'title' => 'Error!',
                'text' => 'Archivo no encontrado',
                'icon' => 'error',
            ]);
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Preview a document (for PDFs and images).
     */
    public function previewDocument(Employee $employee, Document $document)
    {
        // Verificar que el documento pertenece al empleado
        if ($document->employee_id !== $employee->id) {
            Swal::error([
                'title' => 'Error!',
                'text' => 'No autorizado',
                'icon' => 'error',
            ]);
            abort(403, 'No autorizado');
        }

        // Verificar que el archivo existe
        if (!Storage::disk('public')->exists($document->file_path)) {
            Swal::error([
                'title' => 'Error!',
                'text' => 'Archivo no encontrado',
                'icon' => 'error',
            ]);
            abort(404, 'Archivo no encontrado');
        }

        // Solo permitir preview de PDFs e imágenes
        if (!$document->isPdf() && !$document->isImage()) {
            return redirect()->route('employees.documents.download', [$employee, $document]);
        }

        $file = Storage::disk('public')->get($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }

    /**
     * Remove the specified document.
     */
    public function destroyDocument(Employee $employee, Document $document)
    {
        // Verificar que el documento pertenece al empleado
        if ($document->employee_id !== $employee->id) {
            Swal::error([
                'title' => 'Error!',
                'text' => 'No autorizado',
                'icon' => 'error',
            ]);
            abort(403, 'No autorizado');
        }

        // Eliminar archivo físico
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }


        // Eliminar registro
        $document->delete();

        Swal::success([
            'title' => 'Eliminado!',
            'text' => 'Documento eliminado exitosamente',
            'icon' => 'success',
        ]);

        return back();
    }

    /**
     * Get document types for select
     */
    public function getTypes()
    {
        return response()->json(Document::getDocumentTypes());
    }
}
