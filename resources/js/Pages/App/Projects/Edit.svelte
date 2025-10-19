<script>
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { project, employees, statuses, errors = {} } = $props();

    const form = useForm({
        name: project.name,
        code: project.code,
        description: project.description || "",
        start_date: project.start_date,
        end_date: project.end_date || "",
        status: project.status,
        budget: project.budget || "",
        project_manager_id: project.project_manager_id || "",
    });

    function submit(e) {
        e.preventDefault();
        $form.put(`/projects/update/${project.id}`);
    }

    function getStatusBadge(status) {
        const badges = {
            planning: "bg-info bg-opacity-10 text-info",
            active: "bg-success bg-opacity-10 text-success",
            on_hold: "bg-warning bg-opacity-10 text-warning",
            completed: "bg-secondary bg-opacity-10 text-secondary",
            cancelled: "bg-danger bg-opacity-10 text-danger",
        };
        return badges[status] || "bg-secondary bg-opacity-10 text-secondary";
    }
</script>

<AdminLayout>
    <!-- Header -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-1">
                        <i class="bi bi-pencil-square"></i>
                        Editar Proyecto
                    </h5>
                    <p class="text-muted mb-0">
                        Actualiza la información del proyecto <strong
                            >{project.name}</strong
                        >
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <Link href={`/projects`} class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </Link>
                    <Link href="/projects" class="btn btn-outline-primary">
                        <i class="bi bi-grid"></i>
                        Ver Todos
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form onsubmit={submit}>
        <div class="row g-3">
            <!-- Información Básica -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-info-circle"></i>
                            Información Básica
                        </h5>

                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    Nombre del Proyecto
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control {errors.name
                                        ? 'is-invalid'
                                        : ''}"
                                    id="name"
                                    bind:value={$form.name}
                                    placeholder="Ej: Sistema de Gestión"
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
                                <label for="code" class="form-label">
                                    Código
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control {errors.code
                                        ? 'is-invalid'
                                        : ''}"
                                    id="code"
                                    bind:value={$form.code}
                                    placeholder="Ej: PROJ0001"
                                    required
                                />
                                {#if errors.code}
                                    <div class="invalid-feedback">
                                        {errors.code}
                                    </div>
                                {/if}
                                <small class="text-muted">
                                    El código debe ser único
                                </small>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">
                                    Descripción
                                </label>
                                <textarea
                                    class="form-control {errors.description
                                        ? 'is-invalid'
                                        : ''}"
                                    id="description"
                                    bind:value={$form.description}
                                    rows="4"
                                    placeholder="Describe los objetivos y alcance del proyecto..."
                                ></textarea>
                                {#if errors.description}
                                    <div class="invalid-feedback">
                                        {errors.description}
                                    </div>
                                {/if}
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">
                                    Fecha de Inicio
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    class="form-control {errors.start_date
                                        ? 'is-invalid'
                                        : ''}"
                                    id="start_date"
                                    bind:value={$form.start_date}
                                    required
                                />
                                {#if errors.start_date}
                                    <div class="invalid-feedback">
                                        {errors.start_date}
                                    </div>
                                {/if}
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">
                                    Fecha de Finalización
                                </label>
                                <input
                                    type="date"
                                    class="form-control {errors.end_date
                                        ? 'is-invalid'
                                        : ''}"
                                    id="end_date"
                                    bind:value={$form.end_date}
                                />
                                {#if errors.end_date}
                                    <div class="invalid-feedback">
                                        {errors.end_date}
                                    </div>
                                {/if}
                                <small class="text-muted">
                                    Opcional - Define cuando finaliza el
                                    proyecto
                                </small>
                            </div>

                            <!-- Presupuesto -->
                            <div class="col-md-6 mb-3">
                                <label for="budget" class="form-label">
                                    Presupuesto
                                </label>
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
                                <label
                                    for="project_manager_id"
                                    class="form-label"
                                >
                                    Project Manager
                                </label>
                                <select
                                    class="form-select {errors.project_manager_id
                                        ? 'is-invalid'
                                        : ''}"
                                    id="project_manager_id"
                                    bind:value={$form.project_manager_id}
                                >
                                    <option value="">Seleccionar...</option>
                                    {#each employees as employee}
                                        <option value={employee.id}>
                                            {employee.name} - {employee.email}
                                        </option>
                                    {/each}
                                </select>
                                {#if errors.project_manager_id}
                                    <div class="invalid-feedback">
                                        {errors.project_manager_id}
                                    </div>
                                {/if}
                                <small class="text-muted">
                                    Responsable de liderar el proyecto
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Estado -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-flag"></i>
                            Estado del Proyecto
                        </h5>

                        <label for="status" class="form-label">
                            Estado <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select {errors.status
                                ? 'is-invalid'
                                : ''}"
                            id="status"
                            bind:value={$form.status}
                            required
                        >
                            {#each Object.entries(statuses) as [key, label]}
                                <option value={key}>{label}</option>
                            {/each}
                        </select>
                        {#if errors.status}
                            <div class="invalid-feedback">{errors.status}</div>
                        {/if}

                        <!-- Preview del estado -->
                        <div class="mt-3 p-3 rounded bg-light">
                            <small class="text-muted d-block mb-2">
                                Vista previa:
                            </small>
                            <span class="badge {getStatusBadge($form.status)}">
                                {statuses[$form.status]}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Información del Proyecto -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-clock-history"></i>
                            Información de Auditoría
                        </h5>

                        <div class="info-item">
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-calendar-plus"></i> Creado
                            </small>
                            <span class="fw-medium">
                                {new Date(
                                    project.created_at,
                                ).toLocaleDateString("es-CO", {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                })}
                            </span>
                        </div>

                        <div class="info-item">
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-arrow-clockwise"></i> Última actualización
                            </small>
                            <span class="fw-medium">
                                {new Date(
                                    project.updated_at,
                                ).toLocaleDateString("es-CO", {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                })}
                            </span>
                        </div>

                        <div class="info-item">
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-people"></i> Empleados asignados
                            </small>
                            <span class="badge bg-primary">
                                {project.project_assignments?.filter(
                                    (a) => a.is_active,
                                ).length || 0}
                                activos
                            </span>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Los cambios se guardarán al hacer clic en "Actualizar"
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-exclamation-triangle"></i>
                            Los cambios afectarán la información visible del proyecto
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href={`/projects`}
                            class="btn btn-light"
                            disabled={$form.processing}
                        >
                            <i class="bi bi-x-circle"></i>
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
                                <i class="bi bi-check-lg"></i>
                                Actualizar Proyecto
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</AdminLayout>

<style>
    .info-item {
        padding: 0.875rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-item i {
        font-size: 0.875rem;
    }

    /* Efecto hover en el form */
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
