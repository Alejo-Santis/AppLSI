<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class EmergencyContactController extends Controller
{
    /**
     * Display a listing of emergency contacts for an employee.
     */
    public function contacts(Employee $employee)
    {
        $contacts = $employee->emergencyContacts()
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'relationship' => $contact->relationship,
                    'relationship_label' => $contact->relationship_label,
                    'phone' => $contact->phone,
                    'email' => $contact->email,
                    'address' => $contact->address,
                    'is_primary' => $contact->is_primary,
                ];
            });

        return response()->json($contacts);
    }

    /**
     * Store a newly created emergency contact.
     */
    public function storeContact(Request $request, Employee $employee)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'relationship' => ['required', 'in:' . implode(',', array_keys(EmergencyContact::getRelationshipTypes()))],
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:255'],
                'address' => ['nullable', 'string'],
                'is_primary' => ['boolean'],
            ], [
                'name.required' => 'El nombre es obligatorio',
                'relationship.required' => 'La relación es obligatoria',
                'relationship.in' => 'La relación seleccionada no es válida',
                'phone.required' => 'El teléfono es obligatorio',
                'email.email' => 'Ingrese un email válido',
            ]);

            // Si se marca como primario, desmarcar otros contactos primarios
            if ($validated['is_primary'] ?? false) {
                EmergencyContact::where('employee_id', $employee->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Crear contacto
            EmergencyContact::create([
                'employee_id' => $employee->id,
                'name' => $validated['name'],
                'relationship' => $validated['relationship'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_primary' => $validated['is_primary'] ?? false,
            ]);

            Swal::success([
                'title' => ' Creado',
                'text' => 'Contacto creado exitosamente.',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Crear',
                'text' => 'No se pudo crear el contacto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    /**
     * Update the specified emergency contact.
     */
    public function updateContact(Request $request, Employee $employee, EmergencyContact $contact)
    {
        try {
            // Verificar que el contacto pertenece al empleado
            if ($contact->employee_id !== $employee->id) {
                abort(403, 'No autorizado');
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'relationship' => ['required', 'in:' . implode(',', array_keys(EmergencyContact::getRelationshipTypes()))],
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:255'],
                'address' => ['nullable', 'string'],
                'is_primary' => ['boolean'],
            ], [
                'name.required' => 'El nombre es obligatorio',
                'relationship.required' => 'La relación es obligatoria',
                'relationship.in' => 'La relación seleccionada no es válida',
                'phone.required' => 'El teléfono es obligatorio',
                'email.email' => 'Ingrese un email válido',
            ]);

            // Si se marca como primario, desmarcar otros contactos primarios
            if ($validated['is_primary'] ?? false) {
                EmergencyContact::where('employee_id', $employee->id)
                    ->where('id', '!=', $contact->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Actualizar contacto
            $contact->update($validated);

            Swal::success([
                'title' => 'Actualizado',
                'text' => 'Contacto actualizado exitosamente.',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Actualizar',
                'text' => 'No se pudo actualizar el contacto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    /**
     * Remove the specified emergency contact.
     */
    public function destroyContact(Employee $employee, EmergencyContact $contact)
    {
        try {
            // Verificar que el contacto pertenece al empleado
            if ($contact->employee_id !== $employee->id) {
                abort(403, 'No autorizado');
            }

            // Verificar que no sea el único contacto
            $totalContacts = EmergencyContact::where('employee_id', $employee->id)->count();

            if ($totalContacts <= 1) {
                Swal::error([
                    'title' => 'No se puede eliminar',
                    'text' => 'El empleado debe tener al menos un contacto de emergencia',
                    'icon' => 'warning',
                ]);
                return back();
            }

            // Si era el contacto primario, asignar otro como primario
            if ($contact->is_primary) {
                $newPrimary = EmergencyContact::where('employee_id', $employee->id)
                    ->where('id', '!=', $contact->id)
                    ->first();

                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }

            // Eliminar contacto
            $contact->delete();

            Swal::success([
                'title' => 'Eliminaddo',
                'text' => 'Contacto eliminado exitosamente.',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Eliminar',
                'text' => 'No se pudo eliminar el contacto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back();
        }
    }

    /**
     * Get relationship types for select
     */
    public function getRelationshipTypes()
    {
        return response()->json(EmergencyContact::getRelationshipTypes());
    }
}
