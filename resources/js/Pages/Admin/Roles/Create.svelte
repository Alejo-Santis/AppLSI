<script>
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";
    import AdminLayout from "@/Layouts/AdminLayout.svelte";

    let { permissions, errors = {} } = $props();

    const form = useForm({
        name: "",
        description: "",
        permissions: [],
    });

    function submit(e) {
        e.preventDefault();
        $form.post("/admin/roles/store");
    }

    function toggleModule(module) {
        const modulePermissions = permissions[module].map((p) => p.id);
        const allSelected = modulePermissions.every((id) =>
            $form.permissions.includes(id)
        );

        if (allSelected) {
            // Deseleccionar todos del módulo
            $form.permissions = $form.permissions.filter(
                (id) => !modulePermissions.includes(id)
            );
        } else {
            // Seleccionar todos del módulo
            const newPermissions = new Set([
                ...$form.permissions,
                ...modulePermissions,
            ]);
            $form.permissions = Array.from(newPermissions);
        }
    }

    function isModuleSelected(module) {
        const modulePermissions = permissions[module].map((p) => p.id);
        return modulePermissions.every((id) => $form.permissions.includes(id));
    }

    function isModulePartiallySelected(module) {
        const modulePermissions = permissions[module].map((p) => p.id);
        const someSelected = modulePermissions.some((id) =>
            $form.permissions.includes(id)
        );
        const allSelected = modulePermissions.every((id) =>
            $form.permissions.includes(id)
        );
        return someSelected && !allSelected;
    }
</script>

<AdminLayout title="Crear Rol">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-shield-plus"></i>
                            Crear Nuevo Rol
                        </h5>
                        <p class="text-muted mb-0">
                            Define un nuevo rol con permisos personalizados
                        </p>
                    </div>
                    <Link
                        href="/admin/roles"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </Link>
                </div>
            </div>
        </div>

        <form onsubmit={submit}>
            <div class="row">
                <!-- Información Básica -->
                <div class="col-lg-4">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Información del Rol</h5>

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label"
                                    >Nombre del Rol
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    class="form-control {errors.name
                                        ? 'is-invalid'
                                        : ''}"
                                    id="name"
                                    bind:value={$form.name}
                                    placeholder="Ej: Coordinador de Proyectos"
                                    required
                                />
                                {#if errors.name}
                                    <div class="invalid-feedback">
                                        {errors.name}
                                    </div>
                                {/if}
                                <small class="text-muted">
                                    Usa un nombre descriptivo y único
                                </small>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
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
                                    placeholder="Describe las responsabilidades de este rol..."
                                ></textarea>
                                {#if errors.description}
                                    <div class="invalid-feedback">
                                        {errors.description}
                                    </div>
                                {/if}
                            </div>

                            <!-- Resumen -->
                            <div class="alert alert-info">
                                <small>
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Permisos seleccionados:</strong>
                                    {$form.permissions.length}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos -->
                <div class="col-lg-8">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                Asignar Permisos
                                <small class="text-muted">
                                    (Selecciona los permisos para este rol)
                                </small>
                            </h5>

                            {#if errors.permissions}
                                <div class="alert alert-danger">
                                    {errors.permissions}
                                </div>
                            {/if}

                            <div class="accordion" id="permissionsAccordion">
                                {#each Object.entries(permissions) as [module, perms], index}
                                    <div class="accordion-item">
                                        <h2
                                            class="accordion-header"
                                            id="heading{index}"
                                        >
                                            <button
                                                class="accordion-button {index !==
                                                0
                                                    ? 'collapsed'
                                                    : ''}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{index}"
                                            >
                                                <div
                                                    class="d-flex justify-content-between align-items-center w-100 me-3"
                                                >
                                                    <div>
                                                        <i
                                                            class="bi bi-folder2-open me-2"
                                                        ></i>
                                                        {module}
                                                        <span
                                                            class="badge bg-light text-dark ms-2"
                                                        >
                                                            {perms.length} permisos
                                                        </span>
                                                    </div>
                                                    <div>
                                                        {#if isModuleSelected(module)}
                                                            <span
                                                                class="badge bg-success"
                                                            >
                                                                <i
                                                                    class="bi bi-check-circle"
                                                                ></i>
                                                                Todos
                                                            </span>
                                                        {:else if isModulePartiallySelected(module)}
                                                            <span
                                                                class="badge bg-warning"
                                                            >
                                                                <i
                                                                    class="bi bi-dash-circle"
                                                                ></i>
                                                                Parcial
                                                            </span>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div
                                            id="collapse{index}"
                                            class="accordion-collapse collapse {index ===
                                            0
                                                ? 'show'
                                                : ''}"
                                            data-bs-parent="#permissionsAccordion"
                                        >
                                            <div class="accordion-body">
                                                <!-- Seleccionar todos del módulo -->
                                                <div class="mb-3">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick={() =>
                                                            toggleModule(module)}
                                                    >
                                                        <i
                                                            class="bi bi-check-all"
                                                        ></i>
                                                        {isModuleSelected(module)
                                                            ? "Deseleccionar"
                                                            : "Seleccionar"} Todo
                                                    </button>
                                                </div>

                                                <div class="row">
                                                    {#each perms as permission}
                                                        <div
                                                            class="col-md-6 mb-2"
                                                        >
                                                            <div
                                                                class="form-check"
                                                            >
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    id="permission-{permission.id}"
                                                                    bind:group={$form.permissions}
                                                                    value={permission.id}
                                                                />
                                                                <label
                                                                    class="form-check-label"
                                                                    for="permission-{permission.id}"
                                                                >
                                                                    <strong
                                                                        >{permission.name}</strong
                                                                    >
                                                                    <br />
                                                                    <small
                                                                        class="text-muted"
                                                                    >
                                                                        {permission.description}
                                                                    </small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    {/each}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <Link
                            href="/admin/roles"
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
                                Crear Rol
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>