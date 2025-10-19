<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link, router } from "@inertiajs/svelte";

    let { employees, departments, positions, filters = {} } = $props();

    let search = $state(filters.search || "");
    let departmentId = $state(filters.department_id || "all");
    let positionId = $state(filters.position_id || "all");
    let status = $state(filters.status || "all");
    let perPage = $state(filters.per_page || 10);
    let sortField = $state(filters.sort_field || "first_name");
    let sortDirection = $state(filters.sort_direction || "asc");

    let deleteModal = $state({
        show: false,
        employee: null,
    });

    // Aplicar filtros
    function applyFilters() {
        router.get(
            "/employees",
            {
                search: search,
                department_id: departmentId,
                position_id: positionId,
                status: status,
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
        departmentId = "all";
        positionId = "all";
        status = "all";
        perPage = 10;
        sortField = "first_name";
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
    function confirmDelete(employee) {
        deleteModal = { show: true, employee };
    }

    // Eliminar empleado
    function deleteEmployee() {
        if (deleteModal.employee) {
            router.delete(`/employees/delete/${deleteModal.employee.id}`, {
                onSuccess: () => {
                    deleteModal = { show: false, employee: null };
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

    // Badge de estado
    function getStatusBadge(st) {
        const badges = {
            active: "bg-light-success text-success",
            inactive: "bg-light-danger text-danger",
            on_leave: "bg-light-warning text-warning",
            terminated: "bg-light-secondary text-secondary",
        };
        return badges[st] || "bg-light-secondary text-secondary";
    }

    function getStatusLabel(st) {
        const labels = {
            active: "Activo",
            inactive: "Inactivo",
            on_leave: "En Licencia",
            terminated: "Terminado",
        };
        return labels[st] || st;
    }
</script>

<AdminLayout title="Empleados">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-semibold mb-1">
                            <i class="bi bi-person-gear"></i>
                            Gestión de Empleados
                        </h5>
                        <p class="text-muted mb-0">
                            Administra toda la información de tus empleados
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href="/employees/create"
                            class="btn btn-primary d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-plus"></i>
                            Nuevo Empleado
                        </Link>
                        <a
                            href="/employees/export"
                            class="btn btn-outline-success d-flex align-items-center gap-2"
                        >
                            <i class="bi bi-download"></i>
                            <span>Exportar</span>
                        </a>
                        <button
                            type="button"
                            class="btn btn-primary d-flex align-items-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#importEmployeesModal"
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
                    <div class="col-md-3">
                        <label class="form-label" for="search">Buscar</label>
                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Nombre, email..."
                            bind:value={search}
                        />
                    </div>

                    <!-- Departamento -->
                    <div class="col-md-2">
                        <label class="form-label" for="department"
                            >Departamento</label
                        >
                        <select
                            id="department"
                            class="form-select"
                            bind:value={departmentId}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            {#each departments as department}
                                <option value={department.id}
                                    >{department.name}</option
                                >
                            {/each}
                        </select>
                    </div>

                    <!-- Puesto -->
                    <div class="col-md-2">
                        <label class="form-label" for="position">Puesto</label>
                        <select
                            id="position"
                            class="form-select"
                            bind:value={positionId}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            {#each positions as position}
                                <option value={position.id}
                                    >{position.title}</option
                                >
                            {/each}
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2">
                        <label class="form-label" for="estate">Estado</label>
                        <select
                            id="state"
                            class="form-select"
                            bind:value={status}
                            onchange={applyFilters}
                        >
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                            <option value="on_leave">En Licencia</option>
                            <option value="terminated">Terminados</option>
                        </select>
                    </div>

                    <!-- Por página -->
                    <div class="col-md-1">
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
                            <i class="bi bi-arrow-repeat"></i> Limpiar
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
                                    onclick={() => sortBy("first_name")}
                                    style="cursor: pointer"
                                >
                                    Empleado
                                    {#if sortField === "first_name"}
                                        <i
                                            class="bi bi-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th>Puesto</th>
                                <th>Departamento</th>
                                <th
                                    onclick={() => sortBy("hire_date")}
                                    style="cursor: pointer"
                                >
                                    Fecha Contratación
                                    {#if sortField === "hire_date"}
                                        <i
                                            class="bi bi-arrow-{sortDirection ===
                                            'asc'
                                                ? 'up'
                                                : 'down'}"
                                        ></i>
                                    {/if}
                                </th>
                                <th class="text-end">Salario</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if employees.data.length > 0}
                                {#each employees.data as employee}
                                    <tr>
                                        <td>
                                            <div
                                                class="d-flex align-items-center gap-2"
                                            >
                                                <div
                                                    class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center text-uppercase fw-semibold"
                                                    style="width: 40px; height: 40px; font-size: 14px;"
                                                >
                                                    {employee.first_name.charAt(
                                                        0,
                                                    )}{employee.last_name.charAt(
                                                        0,
                                                    )}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {employee.first_name}
                                                        {employee.last_name}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {employee.email}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {#if employee.position}
                                                <span
                                                    class="badge bg-light-info text-info"
                                                >
                                                    {employee.position.title}
                                                </span>
                                            {:else}
                                                <span class="text-muted"
                                                    >N/A</span
                                                >
                                            {/if}
                                        </td>
                                        <td>
                                            {#if employee.department}
                                                <span
                                                    class="badge bg-light-secondary text-secondary"
                                                >
                                                    {employee.department.name}
                                                </span>
                                            {:else}
                                                <span class="text-muted"
                                                    >N/A</span
                                                >
                                            {/if}
                                        </td>
                                        <td>
                                            <small>
                                                {new Date(
                                                    employee.hire_date,
                                                ).toLocaleDateString("es-CO", {
                                                    year: "numeric",
                                                    month: "short",
                                                    day: "numeric",
                                                })}
                                            </small>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold">
                                                ${parseFloat(
                                                    employee.salary,
                                                ).toLocaleString("es-CO")}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge {getStatusBadge(
                                                    employee.status,
                                                )}"
                                            >
                                                {getStatusLabel(
                                                    employee.status,
                                                )}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <Link
                                                    href={`/employees/show/${employee.id}`}
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver detalles"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </Link>
                                                <Link
                                                    href={`/employees/edit/${employee.id}`}
                                                    class="btn btn-sm btn-outline-warning"
                                                    title="Editar"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </Link>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick={() =>
                                                        confirmDelete(employee)}
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
                                            <i class="bi bi-person-slash fs-1"
                                            ></i>
                                            <p class="mt-2">
                                                No se encontraron empleados
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                {#if employees.data.length > 0}
                    <div
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div class="text-muted">
                            Mostrando {employees.from} a {employees.to} de {employees.total}
                            registros
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                {#each employees.links as link}
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
                                (deleteModal = { show: false, employee: null })}
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            ¿Estás seguro de que deseas eliminar al empleado
                            <strong
                                >{deleteModal.employee?.first_name}
                                {deleteModal.employee?.last_name}</strong
                            >?
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
                                (deleteModal = { show: false, employee: null })}
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick={deleteEmployee}
                        >
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- MODAL IMPORTAR EMPLOYEES -->

    <div
        class="modal fade"
        id="importEmployeesModal"
        tabindex="-1"
        aria-labelledby="importEmployeesModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="importEmployeesModalLabel">
                        <i class="bi bi-upload me-2"></i>Importar Empleados
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
                        id="importEmployeesForm"
                        action="/employees/import"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        <div
                            id="dropZoneEmployees"
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
                                id="fileInputEmployees"
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
                        form="importEmployeesForm"
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
            const dropZone = document.getElementById("dropZoneEmployees");
            const fileInput = document.getElementById("fileInputEmployees");

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
