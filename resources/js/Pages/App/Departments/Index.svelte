<script>
    import { Link, router } from "@inertiajs/svelte";
    import { page } from "@inertiajs/svelte";
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";

    let { departments, filters = {} } = $props();

    let search = $state(filters.search || "");
    let status = $state(filters.status || "all");
    let hasManager = $state(filters.has_manager || "all");
    let perPage = $state(filters.per_page || 10);
    let sortField = $state(filters.sort_field || "name");
    let sortDirection = $state(filters.sort_direction || "asc");

    let deleteModal = $state({
        show: false,
        department: null,
    });

    // Aplicar filtros
    function applyFilters() {
        router.get(
            "/departments",
            {
                search: search,
                status: status,
                has_manager: hasManager,
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
        hasManager = "all";
        perPage = 10;
        sortField = "name";
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
    function confirmDelete(department) {
        deleteModal = { show: true, department };
    }

    // Eliminar departamento
    function deleteDepartment() {
        if (deleteModal.department) {
            router.delete(`/departments/delete/${deleteModal.department.id}`, {
                onSuccess: () => {
                    deleteModal = { show: false, department: null };
                },
            });
        }
    }

    // Búsqueda con debounce
    let searchTimeout;
    $effect(() => {
        if (search !== undefined) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        }
    });
</script>

<AdminLayout title="Departamentos">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-building-gear"></i>
                            Gestión de Departamentos
                        </h5>
                        <p class="text-muted mb-0">
                            Administra los departamentos de tu organización
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href="/departments/create"
                            class="btn btn-primary d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-plus"></i>
                            Nuevo Departamento
                        </Link>
                        <a
                            href="/departments/export"
                            class="btn btn-outline-success d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-download"></i>
                            <span>Exportar</span>
                        </a>

                        <button
                            type="button"
                            class="btn btn-primary d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#importModal"
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
                        <label class="form-label" for="code">Buscar</label>
                        <input
                            id="code"
                            type="text"
                            class="form-control"
                            placeholder="Nombre, código o descripción..."
                            bind:value={search}
                        />
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

                    <!-- Manager -->
                    <div class="col-md-2">
                        <label class="form-label" for="manager">Manager</label>
                        <select
                            id="manager"
                            class="form-select"
                            bind:value={hasManager}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            <option value="with">Con Manager</option>
                            <option value="without">Sin Manager</option>
                        </select>
                    </div>

                    <!-- Por página -->
                    <div class="col-md-2">
                        <label class="form-label" for="show">Mostrar</label>
                        <select
                            id="show"
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
                                    onclick={() => sortBy("code")}
                                    style="cursor: pointer"
                                >
                                    Código
                                    {#if sortField === "code"}
                                        <i
                                            class="ti ti-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th
                                    onclick={() => sortBy("name")}
                                    style="cursor: pointer"
                                >
                                    Nombre
                                    {#if sortField === "name"}
                                        <i
                                            class="ti ti-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th>Manager</th>
                                <th class="text-center">Empleados</th>
                                <th class="text-end">Presupuesto</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if departments.data.length > 0}
                                {#each departments.data as department}
                                    <tr>
                                        <td>
                                            <span
                                                class="badge bg-light-primary text-primary"
                                            >
                                                {department.code}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">
                                                    {department.name}
                                                </h6>
                                                {#if department.description}
                                                    <small class="text-muted">
                                                        {department.description.substring(
                                                            0,
                                                            50,
                                                        )}...
                                                    </small>
                                                {/if}
                                            </div>
                                        </td>
                                        <td>
                                            {#if department.manager}
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                >
                                                    <div
                                                        class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px;"
                                                    >
                                                        <i
                                                            class="ti ti-user fs-5"
                                                        ></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fs-3">
                                                            {department.manager
                                                                .first_name}
                                                            {department.manager
                                                                .last_name}
                                                        </h6>
                                                        <small
                                                            class="text-muted"
                                                        >
                                                            {department.manager
                                                                .email}
                                                        </small>
                                                    </div>
                                                </div>
                                            {:else}
                                                <span class="text-muted">
                                                    Sin asignar
                                                </span>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light-secondary text-secondary fs-3"
                                            >
                                                {department.employees?.length ||
                                                    0}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {#if department.budget}
                                                <span class="fw-semibold">
                                                    ${parseFloat(
                                                        department.budget,
                                                    ).toLocaleString("es-CO")}
                                                </span>
                                            {:else}
                                                <span class="text-muted">
                                                    N/A
                                                </span>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            {#if department.is_active}
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
                                                    href={`/departments/show/${department.id}`}
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver detalles"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </Link>
                                                <Link
                                                    href={`/departments/edit/${department.id}`}
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Editar"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </Link>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick={() =>
                                                        confirmDelete(
                                                            department,
                                                        )}
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
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-folder-off fs-9"
                                            ></i>
                                            <p class="mt-2">
                                                No se encontraron departamentos
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                {#if departments.data.length > 0}
                    <div
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div class="text-muted">
                            Mostrando {departments.from} a {departments.to} de {departments.total}
                            registros
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                {#each departments.links as link}
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
                            onclick={() =>
                                (deleteModal = {
                                    show: false,
                                    department: null,
                                })}
                            aria-label="confirmar eliminacion"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            ¿Estás seguro de que deseas eliminar el departamento
                            <strong>{deleteModal.department?.name}</strong>?
                        </p>
                        <p class="text-danger mb-0">
                            <i class="ti ti-alert-triangle"></i> Esta acción no se
                            puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            onclick={() =>
                                (deleteModal = {
                                    show: false,
                                    department: null,
                                })}
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick={deleteDepartment}
                        >
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- MODAL DE IMPORTACIÓN -->s
    <div
        class="modal fade"
        id="importModal"
        tabindex="-1"
        aria-labelledby="importModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-upload me-2"></i>
                        Importar datos
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>
                <div class="modal-body">
                    <form id="importForm" enctype="multipart/form-data">
                        <!-- Zona drag & drop -->
                        <div
                            id="dropZone"
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
                                id="fileInput"
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
                        form="importForm"
                        class="btn btn-success"
                    >
                        <i class="bi bi-check"></i>
                        Importar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS PARA DRAG & DROP -->

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropZone = document.getElementById("dropZone");
            const fileInput = document.getElementById("fileInput");

            // Click en zona para abrir selector
            dropZone.addEventListener("click", () => fileInput.click());

            // Drag & drop
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
                alert(`Archivo seleccionado: ${fileInput.files[0].name}`);
            });

            // Confirmación al seleccionar archivo manualmente
            fileInput.addEventListener("change", () => {
                if (fileInput.files.length > 0) {
                    alert(`Archivo seleccionado: ${fileInput.files[0].name}`);
                }
            });

            // Envío del formulario (aquí puedes llamar tu endpoint Laravel)
            document
                .getElementById("importForm")
                .addEventListener("submit", (e) => {
                    e.preventDefault();
                    const file = fileInput.files[0];
                    if (!file) {
                        alert(
                            "Por favor selecciona un archivo antes de importar.",
                        );
                        return;
                    }
                    alert(`Importando: ${file.name}`);
                    // Aquí podrías hacer una petición POST con fetch() o Axios
                });
        });
    </script>
</AdminLayout>
