<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { position, levels, errors = {} } = $props();

    const form = useForm({
        title: position.title,
        description: position.description || "",
        level: position.level,
        min_salary: position.min_salary || "",
        max_salary: position.max_salary || "",
        is_active: position.is_active,
    });

    function submit(e) {
        e.preventDefault();
        $form.put(`/positions/update/${position.id}`);
    }
</script>

<AdminLayout title="Editar Puesto">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-person-check"></i>
                            Editar Puesto
                        </h5>
                        <p class="text-muted mb-0">
                            Actualiza la información del puesto <strong
                                >{position.title}</strong
                            >
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href={`/positions/${position.id}`}
                            class="btn btn-outline-info"
                        >
                            <i class="bi bi-eye"></i>
                            Ver Detalle
                        </Link>
                        <Link
                            href="/positions"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </Link>
                    </div>
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
                            <h5 class="card-title mb-4">
                                Información del Puesto
                            </h5>

                            <div class="row">
                                <!-- Título -->
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label"
                                        >Título del Puesto
                                        <span class="text-danger">*</span
                                        ></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control {errors.title
                                            ? 'is-invalid'
                                            : ''}"
                                        id="title"
                                        bind:value={$form.title}
                                        placeholder="Ej: Desarrollador Full Stack"
                                        required
                                    />
                                    {#if errors.title}
                                        <div class="invalid-feedback">
                                            {errors.title}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Nivel -->
                                <div class="col-md-4 mb-3">
                                    <label for="level" class="form-label"
                                        >Nivel <span class="text-danger">*</span
                                        ></label
                                    >
                                    <select
                                        class="form-select {errors.level
                                            ? 'is-invalid'
                                            : ''}"
                                        id="level"
                                        bind:value={$form.level}
                                        required
                                    >
                                        {#each Object.entries(levels) as [key, label]}
                                            <option value={key}>{label}</option>
                                        {/each}
                                    </select>
                                    {#if errors.level}
                                        <div class="invalid-feedback">
                                            {errors.level}
                                        </div>
                                    {/if}
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
                                        placeholder="Describe las responsabilidades, requisitos y competencias del puesto..."
                                    ></textarea>
                                    {#if errors.description}
                                        <div class="invalid-feedback">
                                            {errors.description}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Rango Salarial -->
                                <div class="col-12 mb-3">
                                    <label class="form-label" for=""
                                        >Rango Salarial (Opcional)</label
                                    >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label
                                                for="min_salary"
                                                class="form-label small"
                                                >Salario Mínimo</label
                                            >
                                            <div class="input-group">
                                                <span class="input-group-text"
                                                    >$</span
                                                >
                                                <input
                                                    type="number"
                                                    class="form-control {errors.min_salary
                                                        ? 'is-invalid'
                                                        : ''}"
                                                    id="min_salary"
                                                    bind:value={
                                                        $form.min_salary
                                                    }
                                                    placeholder="0.00"
                                                    step="0.01"
                                                    min="0"
                                                />
                                            </div>
                                            {#if errors.min_salary}
                                                <div
                                                    class="invalid-feedback d-block"
                                                >
                                                    {errors.min_salary}
                                                </div>
                                            {/if}
                                        </div>

                                        <div class="col-md-6">
                                            <label
                                                for="max_salary"
                                                class="form-label small"
                                                >Salario Máximo</label
                                            >
                                            <div class="input-group">
                                                <span class="input-group-text"
                                                    >$</span
                                                >
                                                <input
                                                    type="number"
                                                    class="form-control {errors.max_salary
                                                        ? 'is-invalid'
                                                        : ''}"
                                                    id="max_salary"
                                                    bind:value={
                                                        $form.max_salary
                                                    }
                                                    placeholder="0.00"
                                                    step="0.01"
                                                    min="0"
                                                />
                                            </div>
                                            {#if errors.max_salary}
                                                <div
                                                    class="invalid-feedback d-block"
                                                >
                                                    {errors.max_salary}
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
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
                                Los puestos inactivos no aparecerán en los
                                selectores
                            </small>
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-info-circle"></i> Información
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <strong>Creado:</strong><br />
                                        {new Date(
                                            position.created_at,
                                        ).toLocaleDateString("es-CO", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })}
                                    </small>
                                </li>
                                <li class="mb-2">
                                    <small class="text-muted">
                                        <strong>Última actualización:</strong
                                        ><br />
                                        {new Date(
                                            position.updated_at,
                                        ).toLocaleDateString("es-CO", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })}
                                    </small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Advertencias -->
                    {#if position.employees && position.employees.length > 0}
                        <div class="card mt-3 border-warning">
                            <div class="card-body">
                                <h6 class="text-warning mb-2">
                                    <i class="bi bi-exclamation-triangle"></i> Advertencia
                                </h6>
                                <small class="text-muted">
                                    Este puesto tiene <strong
                                        >{position.employees.length}</strong
                                    > empleado(s) asignado(s). Los cambios pueden
                                    afectar su información.
                                </small>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <Link
                            href="/positions"
                            class="btn btn-light"
                            disabled={$form.processing}
                        >
                            <i class="bi bi-x"></i>
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
                                Actualizando...
                            {:else}
                                <i class="bi bi-check"></i>
                                Guardar Cambios
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
