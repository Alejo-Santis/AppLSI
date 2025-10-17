<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";

    let { levels, errors = {} } = $props();

    const form = useForm({
        title: "",
        description: "",
        level: "mid",
        min_salary: "",
        max_salary: "",
        is_active: true,
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/positions/store");
    }
</script>

<AdminLayout title="Crear Puesto">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-person-vcard"></i>
                            Crear Nuevo Puesto
                        </h5>
                        <p class="text-muted mb-0">
                            Define un nuevo puesto para tu organización
                        </p>
                    </div>
                    <Link href="/positions" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
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
                                    <small class="text-muted">
                                        Define un rango de referencia para este
                                        puesto
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
                                Los puestos inactivos no aparecerán en los
                                selectores
                            </small>
                        </div>
                    </div>

                    <!-- Guía de Niveles -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="ti ti-info-circle"></i> Guía de Niveles
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <span class="badge bg-light-info text-info"
                                        >Junior</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >0-2 años de experiencia</small
                                    >
                                </li>
                                <li class="mb-2">
                                    <span
                                        class="badge bg-light-primary text-primary"
                                        >Mid-Level</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >2-5 años de experiencia</small
                                    >
                                </li>
                                <li class="mb-2">
                                    <span
                                        class="badge bg-light-success text-success"
                                        >Senior</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >5+ años de experiencia</small
                                    >
                                </li>
                                <li class="mb-2">
                                    <span
                                        class="badge bg-light-warning text-warning"
                                        >Lead</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >Líder técnico de equipo</small
                                    >
                                </li>
                                <li class="mb-2">
                                    <span
                                        class="badge bg-light-secondary text-secondary"
                                        >Manager</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >Gestión de personas</small
                                    >
                                </li>
                                <li class="mb-2">
                                    <span
                                        class="badge bg-light-danger text-danger"
                                        >Director</span
                                    >
                                    <small class="d-block text-muted mt-1"
                                        >Alta dirección</small
                                    >
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
                            href="/positions"
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
                                <i class="bi bi-check"></i>
                                Crear Puesto
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
