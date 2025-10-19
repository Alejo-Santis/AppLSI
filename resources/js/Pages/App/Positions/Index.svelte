<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link, router } from "@inertiajs/svelte";

    let { positions, filters = {}, levels } = $props();

    let search = $state(filters.search || "");
    let status = $state(filters.status || "all");
    let level = $state(filters.level || "all");
    let perPage = $state(filters.per_page || 10);
    let sortField = $state(filters.sort_field || "title");
    let sortDirection = $state(filters.sort_direction || "asc");
    let searchTimeout;

    let deleteModal = $state({
        show: false,
        position: null,
    });

    // Aplicar filtros
    function applyFilters() {
        router.get(
            "/positions",
            {
                search: search,
                status: status,
                level: level,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }

    // Limpiar filtros
    function clearFilters() {
        search = "";
        status = "all";
        level = "all";
        perPage = 10;
        sortField = "title";
        sortDirection = "asc";
        applyFilters();
    }

    // Ordenar columnas
    function sortBy(field) {
        if (sortField === field) {
            sortDirection = sortDirection === "asc" ? "desc" : "asc";
        } else {
            sortField = field;
            sortDirection = "asc";
        }
        applyFilters();
    }

    // Confirmar eliminación
    function confirmDelete(position) {
        deleteModal = { show: true, position };
    }

    // Eliminar puesto
    function deletePosition() {
        if (deleteModal.position) {
            router.delete(`/positions/delete/${deleteModal.position.id}`, {
                onSuccess: () => {
                    deleteModal = { show: false, position: null };
                },
            });
        }
    }

    // Búsqueda con debounce

    $effect(() => {
        if (search !== undefined) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }
    });

    // Badge de nivel
    function getLevelBadgeClass(lvl) {
        const badges = {
            junior: "bg-light-info text-info",
            mid: "bg-light-primary text-primary",
            senior: "bg-light-success text-success",
            lead: "bg-light-warning text-warning",
            manager: "bg-light-secondary text-secondary",
            director: "bg-light-danger text-danger",
        };
        return badges[lvl] || "bg-light-secondary text-secondary";
    }
</script>

<AdminLayout title="Puestos">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-person-vcard"></i>
                            Gestión de Puestos
                        </h5>
                        <p class="text-muted mb-0">
                            Administra los puestos y roles de tu organización
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href="/positions/create"
                            class="btn btn-primary d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-plus"></i>
                            Nuevo Puesto
                        </Link>
                        <a
                            href="/positions/export"
                            class="btn btn-outline-success d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-download"></i>
                            <span>Exportar</span>
                        </a>
                        <button
                            type="button"
                            class="btn btn-primary d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#importPositionsModal"
                        >
                            <i class="bi bi-upload"></i>
                            <span>Importar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Búsqueda -->
                    <div class="col-md-4">
                        <label class="form-label" for="search">Buscar</label>
                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Título o descripción..."
                            bind:value={search}
                        />
                    </div>

                    <!-- Nivel -->
                    <div class="col-md-2">
                        <label class="form-label" for="level">Nivel</label>
                        <select
                            id="level"
                            class="form-select"
                            bind:value={level}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            {#each Object.entries(levels) as [key, label]}
                                <option value={key}>{label}</option>
                            {/each}
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2">
                        <label class="form-label" for="state">Estado</label>
                        <select
                            id="state"
                            class="form-select"
                            bind:value={status}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </div>

                    <!-- Por página -->
                    <div class="col-md-2">
                        <label class="form-label" for="filters">Mostrar</label>
                        <select
                            id="filters"
                            class="form-select"
                            bind:value={perPage}
                            onchange={applyFilters}
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Botón limpiar -->
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

        <!-- Tabla -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th
                                    onclick={() => sortBy("title")}
                                    style="cursor: pointer"
                                >
                                    Puesto
                                    {#if sortField === "title"}
                                        <i
                                            class="ti ti-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th
                                    onclick={() => sortBy("level")}
                                    style="cursor: pointer"
                                >
                                    Nivel
                                    {#if sortField === "level"}
                                        <i
                                            class="ti ti-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th>Rango Salarial</th>
                                <th class="text-center">Empleados</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if positions.data.length > 0}
                                {#each positions.data as position}
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">
                                                    {position.title}
                                                </h6>
                                                {#if position.description}
                                                    <small class="text-muted">
                                                        {position.description.substring(
                                                            0,
                                                            60,
                                                        )}...
                                                    </small>
                                                {/if}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {getLevelBadgeClass(
                                                    position.level,
                                                )}"
                                            >
                                                {levels[position.level]}
                                            </span>
                                        </td>
                                        <td>
                                            {#if position.min_salary && position.max_salary}
                                                <div>
                                                    <small
                                                        class="text-muted d-block"
                                                    >
                                                        Mín: ${parseFloat(
                                                            position.min_salary,
                                                        ).toLocaleString(
                                                            "es-CO",
                                                        )}
                                                    </small>
                                                    <small class="text-muted">
                                                        Máx: ${parseFloat(
                                                            position.max_salary,
                                                        ).toLocaleString(
                                                            "es-CO",
                                                        )}
                                                    </small>
                                                </div>
                                            {:else}
                                                <span class="text-muted">
                                                    No definido
                                                </span>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light-secondary text-secondary fs-3"
                                            >
                                                {position.employees?.length ||
                                                    0}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {#if position.is_active}
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
                                            <div class="btn-group" role="group">
                                                <Link
                                                    href={`/positions/show/${position.id}`}
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver detalles"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </Link>
                                                <Link
                                                    href={`/positions/edit/${position.id}`}
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Editar"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </Link>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick={() =>
                                                        confirmDelete(position)}
                                                    title="Eliminar"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            {:else}
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-briefcase-off fs-9"
                                            ></i>
                                            <p class="mt-2">
                                                No se encontraron puestos
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                {#if positions.data.length > 0}
                    <div
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div class="text-muted">
                            Mostrando {positions.from} a {positions.to} de {positions.total}
                            registros
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                {#each positions.links as link}
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

    <!-- Modal de confirmación de eliminación -->
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
                                (deleteModal = { show: false, position: null })}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            ¿Estás seguro de que deseas eliminar el puesto
                            <strong>{deleteModal.position?.title}</strong>?
                        </p>
                        <p class="text-danger mb-0">
                            <i class="bi bi-alert-triangle"></i> Esta acción no se
                            puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={() =>
                                (deleteModal = { show: false, position: null })}
                        >
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick={deletePosition}
                        >
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- MODAL IMPORTAR POSITIONS -->

    <div
        class="modal fade"
        id="importPositionsModal"
        tabindex="-1"
        aria-labelledby="importPositionsModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="importPositionsModalLabel">
                        <i class="bi bi-upload me-2"></i>Importar Puestos
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>
                <div class="modal-body">
                    <form
                        id="importPositionsForm"
                        action="/positions/import"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        <div
                            id="dropZonePositions"
                            class="border border-2 border-dashed rounded p-4 text-center bg-light"
                            style="cursor: pointer"
                        >
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary"
                            ></i>
                            <p class="mt-2 mb-1">Arrastra tu archivo aquí</p>
                            <small class="text-muted d-block mb-2"
                                >o haz clic para seleccionarlo</small
                            >
                            <input
                                type="file"
                                id="fileInputPositions"
                                name="file"
                                class="d-none"
                                accept=".csv,.xlsx,.xls"
                            />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        form="importPositionsForm"
                        class="btn btn-success"
                    >
                        <i class="bi bi-check"></i>
                        Importar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropZone = document.getElementById("dropZonePositions");
            const fileInput = document.getElementById("fileInputPositions");

            dropZone.addEventListener("click", () => fileInput.click());

            dropZone.addEventListener("dragover", (e) => {
                e.preventDefault();
                dropZone.classList.add("border-primary", "bg-white");
            });

            dropZone.addEventListener("dragleave", () => {
                dropZone.classList.remove("border-primary", "bg-white");
            });

            dropZone.addEventListener("drop", (e) => {
                e.preventDefault();
                dropZone.classList.remove("border-primary", "bg-white");
                fileInput.files = e.dataTransfer.files;
            });
        });
    </script>
</AdminLayout>
