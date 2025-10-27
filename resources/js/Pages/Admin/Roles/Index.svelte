<script>
    import { Link, router } from "@inertiajs/svelte";
    import AdminLayout from "@/Layouts/AdminLayout.svelte";

    let { roles, filters = {} } = $props();

    let search = $state(filters.search || "");
    let perPage = $state(filters.per_page || 10);
    let deleteModal = $state({ show: false, role: null });

    let searchTimeout;

    function applyFilters() {
        router.get(
            "/admin/roles",
            { search, per_page: perPage },
            { preserveState: true, preserveScroll: true },
        );
    }

    function clearFilters() {
        search = "";
        perPage = 10;
        applyFilters();
    }

    function confirmDelete(role) {
        deleteModal = { show: true, role };
    }

    function deleteRole() {
        if (deleteModal.role) {
            router.delete(`/admin/roles/${deleteModal.role.id}`, {
                onSuccess: () => {
                    deleteModal = { show: false, role: null };
                },
            });
        }
    }

    $effect(() => {
        if (search !== undefined) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }
    });
</script>

<AdminLayout title="Gestión de Roles">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-shield-lock"></i>
                            Gestión de Roles
                        </h5>
                        <p class="text-muted mb-0">
                            Administra los roles y permisos del sistema
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href="/admin/roles/create"
                            class="btn btn-primary d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-plus"></i>
                            Nuevo Rol
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="search">Buscar</label>
                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Nombre o descripción..."
                            bind:value={search}
                        />
                    </div>

                    <div class="col-md-2">
                        <label class="form-label" for="perPage">Mostrar</label>
                        <select
                            id="perPage"
                            class="form-select"
                            bind:value={perPage}
                            onchange={applyFilters}
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            class="btn btn-outline-secondary w-100"
                            onclick={clearFilters}
                        >
                            <i class="bi bi-funnel"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Roles -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-center">Permisos</th>
                                <th class="text-center">Usuarios</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if roles.data.length > 0}
                                {#each roles.data as role}
                                    <tr>
                                        <td>
                                            <div
                                                class="d-flex align-items-center gap-2"
                                            >
                                                <div
                                                    class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;"
                                                >
                                                    <i
                                                        class="bi bi-shield-check fs-5"
                                                    ></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {role.name}
                                                    </h6>
                                                    {#if ["Admin", "HR", "Manager", "Employee"].includes(role.name)}
                                                        <span
                                                            class="badge bg-light-info text-info"
                                                        >
                                                            Sistema
                                                        </span>
                                                    {/if}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {role.description ||
                                                    "Sin descripción"}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light-success text-success fs-3"
                                            >
                                                {role.permissions_count}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light-secondary text-secondary fs-3"
                                            >
                                                {role.users_count}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <Link
                                                    href={`/admin/roles/${role.id}/permissions`}
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Gestionar permisos"
                                                >
                                                    <i class="bi bi-key"></i>
                                                </Link>
                                                <Link
                                                    href={`/admin/roles/${role.id}/edit`}
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Editar"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </Link>
                                                {#if !["Admin", "HR", "Manager", "Employee"].includes(role.name)}
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick={() =>
                                                            confirmDelete(role)}
                                                        title="Eliminar"
                                                    >
                                                        <i class="bi bi-trash"
                                                        ></i>
                                                    </button>
                                                {/if}
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            {:else}
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-shield-x fs-1"></i>
                                            <p class="mt-2">
                                                No se encontraron roles
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                {#if roles.data.length > 0}
                    <div
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div class="text-muted">
                            Mostrando {roles.from} a {roles.to} de {roles.total}
                            registros
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                {#each roles.links as link}
                                    <li
                                        class="page-item {link.active
                                            ? 'active'
                                            : ''} {!link.url ? 'disabled' : ''}"
                                    >
                                        {#if link.url}
                                            <Link
                                                href={link.url}
                                                class="page-link"
                                                preserveState
                                            >
                                                {@html link.label}
                                            </Link>
                                        {:else}
                                            <span class="page-link">
                                                {@html link.label}
                                            </span>
                                        {/if}
                                    </li>
                                {/each}
                            </ul>
                        </nav>
                    </div>
                {/if}
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    {#if deleteModal.show}
        <div
            class="modal fade show d-block"
            tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="close"
                            onclick={() =>
                                (deleteModal = { show: false, role: null })}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            ¿Estás seguro de que deseas eliminar el rol
                            <strong>{deleteModal.role?.name}</strong>?
                        </p>
                        <p class="text-danger mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Esta acción
                            no se puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={() =>
                                (deleteModal = { show: false, role: null })}
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick={deleteRole}
                        >
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</AdminLayout>
