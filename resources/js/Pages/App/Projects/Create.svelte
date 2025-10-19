<script>
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { employees, suggestedCode, statuses, errors = {} } = $props();

    const form = useForm({
        name: "",
        code: suggestedCode || "",
        description: "",
        start_date: "",
        end_date: "",
        status: "planning",
        budget: "",
        project_manager_id: "",
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/projects/store");
    }

    function generateCode() {
        $form.code = suggestedCode;
    }
</script>

<AdminLayout>
    <!-- Header -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-1">
                        <i class="bi bi-kanban"></i>
                        Crear Nuevo Proyecto
                    </h5>
                    <p class="text-muted mb-0">
                        Complete la información del proyecto
                    </p>
                </div>
                <Link href="/projects" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </Link>
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
                        <h5 class="card-title mb-4">Información Básica</h5>

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
                                <div class="input-group">
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
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        onclick={generateCode}
                                        aria-label="generate-code"
                                    >
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                {#if errors.code}
                                    <div class="invalid-feedback d-block">
                                        {errors.code}
                                    </div>
                                {/if}
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
                                    Opcional - Puedes definirla después
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
                        <h5 class="card-title mb-3">Estado del Proyecto</h5>

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

                        <small class="text-muted d-block mt-2">
                            Selecciona el estado actual del proyecto
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
                                    <i class="bi bi-check"></i> Los campos con
                                    <span class="text-danger">*</span> son obligatorios
                                </small>
                            </li>
                            <li class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-check"></i> El código debe ser
                                    único
                                </small>
                            </li>
                            <li class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-check"></i> Podrás asignar empleados
                                    después
                                </small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <Link
                        href="/projects"
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
                            <span class="spinner-border spinner-border-sm me-2"
                            ></span>
                            Guardando...
                        {:else}
                            <i class="bi bi-check-lg"></i>
                            Crear Proyecto
                        {/if}
                    </button>
                </div>
            </div>
        </div>
    </form>
</AdminLayout>
