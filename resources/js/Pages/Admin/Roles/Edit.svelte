<script>
    import { Link } from "@inertiajs/svelte";
    import { useForm } from "@inertiajs/svelte";
    import AdminLayout from "@/Layouts/AdminLayout.svelte";

    let { role, permissions, errors = {} } = $props();

    const form = useForm({
        id: role.id,
        name: role.name,
        description: role.description || "",
        permissions: role.permissions.map((p) => p.id),
    });

    function submit(e) {
        e.preventDefault();
        $form.put(`/admin/roles/${role.id}`);
    }

    function toggleModule(module) {
        const modulePermissions = permissions[module].map((p) => p.id);
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

    function isModuleSelected(module) {
        const modulePermissions = permissions[module].map((p) => p.id);
        return modulePermissions.every((id) => $form.permissions.includes(id));
    }
</script>

<AdminLayout title="Editar Rol">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-pencil"></i> Editar Rol
                        </h5>
                        <p class="text-muted mb-0">
                            Complete la información del rol
                        </p>
                    </div>
                    <Link href="/admin/roles" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </Link>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form onsubmit={submit}>
            <div class="row">
                <!-- Campo de Nombre -->
                <div class="col-12 mb-3">
                    <label class="form-label" for="name">Nombre</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control {errors.name ? 'is-invalid' : ''}"
                        bind:value={$form.name}
                    />
                    {#if errors.name}
                        <div class="invalid-feedback">{errors.name}</div>
                    {/if}
                </div>

                <!-- Campo de Descripción -->
                <div class="col-12 mb-3">
                    <label class="form-label" for="description"
                        >Descripción</label
                    >
                    <textarea
                        id="description"
                        name="description"
                        class="form-control {errors.description
                            ? 'is-invalid'
                            : ''}"
                        bind:value={$form.description}
                    ></textarea>
                    {#if errors.description}
                        <div class="invalid-feedback">{errors.description}</div>
                    {/if}
                </div>

                <!-- Permisos por Módulo -->
                {#each Object.keys(permissions) as module}
                    <div class="col-12 mb-3">
                        <h4 class="mb-3">{module}</h4>
                        {#each permissions[module] as permission}
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    id={`permission-${permission.id}`}
                                    name="permissions"
                                    value={permission.id}
                                    class="form-check-input"
                                    onchange={(e) => {
                                        const checked = e.target.checked;
                                        $form.permissions = checked
                                            ? [
                                                  ...$form.permissions,
                                                  permission.id,
                                              ]
                                            : $form.permissions.filter(
                                                  (id) => id !== permission.id,
                                              );
                                    }}
                                    checked={$form.permissions.includes(
                                        permission.id,
                                    )}
                                />
                                <label
                                    class="form-check-label"
                                    for={`permission-${permission.id}`}
                                >
                                    {permission.name}
                                </label>
                            </div>
                        {/each}
                    </div>
                {/each}

                <!-- Botones -->
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-1">
                        <i class="bi bi-check"></i> Guardar
                    </button>
                    <Link href="/admin/roles" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </Link>
                </div>
            </div>
        </form>
    </div>
</AdminLayout>
