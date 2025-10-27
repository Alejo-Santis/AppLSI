<script>
    import { Link, router } from "@inertiajs/svelte";
    import AdminLayout from "@/Layouts/AdminLayout.svelte";

    let { users, roles, filters = {} } = $props();

    let search = $state(filters.search || "");
    let roleFilter = $state(filters.role || "all");
    let statusFilter = $state(filters.status || "all");
    let perPage = $state(filters.per_page || 15);

    let searchTimeout;

    function applyFilters() {
        router.get(
            "/admin/users",
            {
                search,
                role: roleFilter,
                status: statusFilter,
                per_page: perPage,
            },
            { preserveState: true, preserveScroll: true }
        );
    }

    function clearFilters() {
        search = "";
        roleFilter = "all";
        statusFilter = "all";
        perPage = 15;
        applyFilters();
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

<AdminLayout title="Gestión de Usuarios">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-people"></i>
                            Gestión de Usuarios
                        </h5>
                        <p class="text-muted mb-0">
                            Administra roles y permisos de usuarios
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="search">Buscar</label>
                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Nombre o email..."
                            bind:value={search}
                        />
                    </div>

                    <div class="col-md-2">
                        <label class="form-label" for="roleFilter"
                            >Rol Básico</label
                        >
                        <select
                            id="roleFilter"
                            class="form-select"
                            bind:value={roleFilter}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            <option value="admin">Admin</option>
                            <option value="hr">HR</option>
                            <option value="manager">Manager</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label" for="statusFilter"
                            >Estado</label
                        >
                        <select
                            id="statusFilter"
                            class="form-select"
                            bind:value={statusFilter}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label" for="perPage">Mostrar</label>
                        <select
                            id="perPage"
                            class="form-select"
                            bind:value={perPage}
                            onchange={applyFilters}
                        >
                            <option value="15">15</option>
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

        <!-- Tabla de Usuarios -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol Básico</th>
                                <th>Roles Spatie</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Último Acceso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if users.data.length > 0}
                                {#each users.data as user}
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
                                                        class="bi bi-person fs-5"
                                                    ></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {user.name}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {user.email}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light-{user.role ===
                                                'admin'
                                                    ? 'danger'
                                                    : user.role === 'hr'
                                                      ? 'info'
                                                      : user.role === 'manager'
                                                        ? 'warning'
                                                        : 'secondary'} text-{user.role ===
                                                'admin'
                                                    ? 'danger'
                                                    : user.role === 'hr'
                                                      ? 'info'
                                                      : user.role === 'manager'
                                                        ? 'warning'
                                                        : 'secondary'}"
                                            >
                                                {user.role.toUpperCase()}
                                            </span>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex flex-wrap gap-1"
                                            >
                                                {#if user.roles && user.roles.length > 0}
                                                    {#each user.roles as role}
                                                        <span
                                                            class="badge bg-light-primary text-primary"
                                                        >
                                                            {role.name}
                                                        </span>
                                                    {/each}
                                                {:else}
                                                    <span class="text-muted"
                                                        >Sin rol asignado</span
                                                    >
                                                {/if}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {#if user.is_active}
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
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">
                                                {#if user.last_login}
                                                    {new Date(
                                                        user.last_login,
                                                    ).toLocaleDateString(
                                                        "es-CO",
                                                    )}
                                                {:else}
                                                    Nunca
                                                {/if}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div
                                                class="btn-group"
                                                role="group"
                                            >
                                                <Link
                                                    href={`/admin/users/${user.id}/roles`}
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Gestionar roles"
                                                >
                                                    <i
                                                        class="bi bi-shield-lock"
                                                    ></i>
                                                </Link>
                                                <Link
                                                    href={`/admin/users/${user.id}/permissions`}
                                                    class="btn btn-sm btn-outline-info"
                                                    title="Ver permisos"
                                                >
                                                    <i class="bi bi-key"></i>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            {:else}
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i
                                                class="bi bi-people-fill fs-1"
                                            ></i>
                                            <p class="mt-2">
                                                No se encontraron usuarios
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                {#if users.data.length > 0}
                    <div
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div class="text-muted">
                            Mostrando {users.from} a {users.to} de {users.total}
                            registros
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                {#each users.links as link}
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
</AdminLayout>