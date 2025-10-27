<script>
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";
    import AdminLayout from "@/Layouts/AdminLayout.svelte";

    let { role, allPermissions, rolePermissions, errors = {} } = $props();

    const form = useForm({
        permissions: rolePermissions,
    });

    function submit(e) {
        e.preventDefault();
        $form.post(`/admin/roles/${role.id}/permissions`);
    }

    function toggleModule(module) {
        const modulePermissions = allPermissions[module].map((p) => p.id);
        const allSelected = modulePermissions.every((id) =>
            $form.permissions.includes(id),
        );

        if (allSelected) {
            $form.permissions = $form.permissions.filter(
                (id) => !modulePermissions.includes(id),
            );
        } else {
            const newPermissions = new Set([
                ...$form.permissions,
                ...modulePermissions,
            ]);
            $form.permissions = Array.from(newPermissions);
        }
    }

    function selectAll() {
        const allIds = Object.values(allPermissions)
            .flat()
            .map((p) => p.id);
        $form.permissions = allIds;
    }

    function deselectAll() {
        $form.permissions = [];
    }

    function isModuleSelected(module) {
        const modulePermissions = allPermissions[module].map((p) => p.id);
        return modulePermissions.every((id) => $form.permissions.includes(id));
    }

    function isModulePartiallySelected(module) {
        const modulePermissions = allPermissions[module].map((p) => p.id);
        const someSelected = modulePermissions.some((id) =>
            $form.permissions.includes(id),
        );
        const allSelected = modulePermissions.every((id) =>
            $form.permissions.includes(id),
        );
        return someSelected && !allSelected;
    }
</script>

<AdminLayout title="Permisos del Rol">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-key"></i>
                            Gestionar Permisos: {role.name}
                        </h5>
                        <p class="text-muted mb-0">
                            Asigna o remueve permisos de este rol
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href={`/admin/roles/${role.id}/edit`}
                            class="btn btn-outline-warning"
                        >
                            <i class="bi bi-pencil"></i>
                            Editar Rol
                        </Link>
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
        </div>

        <form onsubmit={submit}>
            <div class="row">
                <!-- Información del Rol -->
                <div class="col-lg-4">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Información del Rol</h5>

                            <div class="mb-3">
                                <label class="form-label" for="name"
                                    ><strong>Nombre:</strong></label
                                >
                                <div>
                                    <h5 class="mb-0" id="name">{role.name}</h5>
                                </div>
                            </div>

                            {#if role.description}
                                <div class="mb-3">
                                    <label class="form-label" for="description"
                                        ><strong>Descripción:</strong></label
                                    >
                                    <p class="text-muted mb-0" id="description">
                                        {role.description}
                                    </p>
                                </div>
                            {/if}

                            <!-- Resumen de Permisos -->
                            <div class="alert alert-primary">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Permisos Seleccionados</strong>
                                </div>
                                <h3 class="mb-0 text-center">
                                    {$form.permissions.length}
                                </h3>
                                <small class="d-block text-center">
                                    de {Object.values(allPermissions).flat()
                                        .length} disponibles
                                </small>
                            </div>

                            <!-- Acciones Rápidas -->
                            <div class="d-grid gap-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-success btn-sm"
                                    onclick={selectAll}
                                >
                                    <i class="bi bi-check-all"></i>
                                    Seleccionar Todos
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick={deselectAll}
                                >
                                    <i class="bi bi-x-circle"></i>
                                    Deseleccionar Todos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos por Módulo -->
                <div class="col-lg-8">
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                Permisos Disponibles
                            </h5>

                            {#if errors.permissions}
                                <div class="alert alert-danger">
                                    {errors.permissions}
                                </div>
                            {/if}

                            <div class="accordion" id="permissionsAccordion">
                                {#each Object.entries(allPermissions) as [module, perms], index}
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
                                                        <strong>{module}</strong
                                                        >
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
                                                        {:else}
                                                            <span
                                                                class="badge bg-secondary"
                                                            >
                                                                <i
                                                                    class="bi bi-circle"
                                                                ></i>
                                                                Ninguno
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
                                                <!-- Botón para seleccionar/deseleccionar módulo -->
                                                <div class="mb-3">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm {isModuleSelected(
                                                            module,
                                                        )
                                                            ? 'btn-outline-danger'
                                                            : 'btn-outline-primary'}"
                                                        onclick={() =>
                                                            toggleModule(
                                                                module,
                                                            )}
                                                    >
                                                        <i
                                                            class="bi bi-{isModuleSelected(
                                                                module,
                                                            )
                                                                ? 'x-circle'
                                                                : 'check-all'}"
                                                        ></i>
                                                        {isModuleSelected(
                                                            module,
                                                        )
                                                            ? "Deseleccionar"
                                                            : "Seleccionar"} Todo
                                                        el Módulo
                                                    </button>
                                                </div>

                                                <div class="row">
                                                    {#each perms as permission}
                                                        <div
                                                            class="col-md-6 mb-3"
                                                        >
                                                            <div
                                                                class="card {$form.permissions.includes(
                                                                    permission.id,
                                                                )
                                                                    ? 'border-success'
                                                                    : ''}"
                                                            >
                                                                <div
                                                                    class="card-body p-3"
                                                                >
                                                                    <div
                                                                        class="form-check"
                                                                    >
                                                                        <input
                                                                            class="form-check-input"
                                                                            type="checkbox"
                                                                            id="permission-{permission.id}"
                                                                            bind:group={
                                                                                $form.permissions
                                                                            }
                                                                            value={permission.id}
                                                                        />
                                                                        <label
                                                                            class="form-check-label"
                                                                            for="permission-{permission.id}"
                                                                        >
                                                                            <strong
                                                                                class="d-block"
                                                                            >
                                                                                <i
                                                                                    class="bi bi-key-fill text-primary me-1"

                                                                                ></i>
                                                                                {permission.name}
                                                                            </strong>
                                                                            <small
                                                                                class="text-muted"
                                                                            >
                                                                                {permission.description}
                                                                            </small>
                                                                        </label>
                                                                    </div>
                                                                </div>
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
                                Guardar Permisos
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
