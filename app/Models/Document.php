<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'title',
        'document_type',
        'file_path',
        'file_size',
        'uploaded_by',
        'upload_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'upload_date' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    /**
     * Relación: Empleado al que pertenece el documento
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación: Usuario que subió el documento
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope: Buscar documentos
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('document_type', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Por tipo de documento
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope: Por empleado
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Accessor: Tamaño del archivo formateado
     */
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Accessor: Extensión del archivo
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    /**
     * Accessor: Nombre del archivo
     */
    public function getFileNameAttribute()
    {
        return basename($this->file_path);
    }

    /**
     * Método: Tipos de documentos disponibles
     */
    public static function getDocumentTypes(): array
    {
        return [
            'contract' => 'Contrato Laboral',
            'evaluation' => 'Evaluación de Desempeño',
            'certificate' => 'Certificado',
            'id_document' => 'Documento de Identidad',
            'tax_form' => 'Formulario Fiscal',
            'resume' => 'Hoja de Vida',
            'diploma' => 'Diploma/Título',
            'recommendation' => 'Carta de Recomendación',
            'other' => 'Otro',
        ];
    }

    /**
     * Método: Extensiones permitidas
     */
    public static function getAllowedExtensions(): array
    {
        return ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx'];
    }

    /**
     * Método: Tamaño máximo permitido en bytes (10MB)
     */
    public static function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }

    /**
     * Método: Verificar si es un PDF
     */
    public function isPdf(): bool
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Método: Verificar si es una imagen
     */
    public function isImage(): bool
    {
        return in_array(strtolower($this->file_extension), ['jpg', 'jpeg', 'png', 'gif']);
    }
}
