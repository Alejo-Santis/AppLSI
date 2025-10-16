<script>
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { employees, suggestedCode, errors = {} } = $props();

    const form = useForm({
        name: "",
        code: suggestedCode || "",
        description: "",
        budget: "",
        manager_id: "",
        is_active: true,
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/departments/store/department");
    }

    function generateCode() {
        $form.code = suggestedCode;
    }
</script>

<AdminLayout title="Crear Departamento">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            Crear Nuevo Departamento
                        </h5>
                        <p class="text-muted mb-0">
                            Complete la información del departamento
                        </p>
                    </div>
                    <Link href="/departments" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i>
                        Volver
                    </Link>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form onsubmit={submit}>
            <div class="row">
                <!-- Información Básica -->
                <div class="col-lg-8">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Información Básica</h5>

                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label"
                                        >Nombre del Departamento
                                        <span class="text-danger">*</span
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control {errors.name
                                            ? 'is-invalid'
                                            : ''}"
                                        id="name"
                                        bind:value={$form.name}
                                        placeholder="Ej: Recursos Humanos"
                                        required
                                    />
                                    {#if errors.name}
                                        <div class="invalid-feedback">
                                            {errors.name}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Código -->
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label"
                                        >Código
                                        <span class="text-danger">*</span
                                        ></label
                                    >
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control {errors.code
                                                ? 'is-invalid'
                                                : ''}"
                                            id="code"
                                            bind:value={$form.code}
                                            placeholder="Ej: DEPT0001"
                                            required
                                        />
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            onclick={generateCode}
                                            title="Generar código automático"
                                        >
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    {#if errors.code}
                                        <div class="invalid-feedback d-block">
                                            {errors.code}
                                        </div>
                                    {/if}
                                    <small class="text-muted">
                                        El código debe ser único
                                    </small>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label"
                                        >Descripción</label
                                    >
                                    <textarea
                                        class="form-control {errors.description
                                            ? 'is-invalid'
                                            : ''}"
                                        id="description"
                                        bind:value={$form.description}
                                        rows="4"
                                        placeholder="Describe las funciones y responsabilidades del departamento..."
                                    ></textarea>
                                    {#if errors.description}
                                        <div class="invalid-feedback">
                                            {errors.description}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Presupuesto -->
                                <div class="col-md-6 mb-3">
                                    <label for="budget" class="form-label"
                                        >Presupuesto Anual</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input
                                            type="number"
                                            class="form-control {errors.budget
                                                ? 'is-invalid'
                                                : ''}"
                                            id="budget"
                                            bind:value={$form.budget}
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                        />
                                    </div>
                                    {#if errors.budget}
                                        <div class="invalid-feedback d-block">
                                            {errors.budget}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Manager -->
                                <div class="col-md-6 mb-3">
                                    <label for="manager_id" class="form-label"
                                        >Manager / Jefe</label
                                    >
                                    <select
                                        class="form-select {errors.manager_id
                                            ? 'is-invalid'
                                            : ''}"
                                        id="manager_id"
                                        bind:value={$form.manager_id}
                                    >
                                        <option value="">
                                            Seleccionar empleado...
                                        </option>
                                        {#each employees as employee}
                                            <option value={employee.id}>
                                                {employee.name} - {employee.email}
                                            </option>
                                        {/each}
                                    </select>
                                    {#if errors.manager_id}
                                        <div class="invalid-feedback">
                                            {errors.manager_id}
                                        </div>
                                    {/if}
                                    <small class="text-muted">
                                        Opcional - Puedes asignarlo después
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="col-lg-4">
                    <!-- Estado -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Estado</h5>

                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    bind:checked={$form.is_active}
                                />
                                <label class="form-check-label" for="is_active">
                                    {#if $form.is_active}
                                        <span
                                            class="badge bg-light-success text-success"
                                        >
                                            Activo
                                        </span>
                                    {:else}
                                        <span
                                            class="badge bg-light-danger text-danger"
                                        >
                                            Inactivo
                                        </span>
                                    {/if}
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Los departamentos inactivos no aparecerán en los
                                selectores
                            </small>
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="ti ti-info-circle"></i> Información
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="ti ti-check"></i> Los campos
                                        marcados con
                                        <span class="text-danger">*</span> son obligatorios
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="ti ti-check"></i> El código debe
                                        ser único en el sistema
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <i class="ti ti-check"></i> Puedes asignar
                                        empleados después de crear el departamento
                                    </small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <Link
                            href="/departments"
                            class="btn btn-light"
                            disabled={$form.processing}
                        >
                            <i class="ti ti-x"></i>
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardando...
                            {:else}
                                <i class="ti ti-check"></i>
                                Crear Departamento
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
