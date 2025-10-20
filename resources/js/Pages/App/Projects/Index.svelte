<script>
    import { Link, router } from "@inertiajs/svelte";
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";
    import DownloadTemplateButton from "../../../Components/DownloadTemplateButton.svelte";
    import ImportModal from "../../../Components/ImportModal.svelte";

    let { projects, filters = {}, statuses } = $props();

    let search = $state(filters.search || "");
    let status = $state(filters.status || "all");
    let perPage = $state(filters.per_page || 10);
    let sortField = $state(filters.sort_field || "name");
    let sortDirection = $state(filters.sort_direction || "asc");
    let showImportModal = $state(false);

    let deleteModal = $state({
        show: false,
        project: null,
    });

    function applyFilters() {
        router.get(
            "/projects",
            {
                search: search,
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

    function clearFilters() {
        search = "";
        status = "all";
        perPage = 10;
        sortField = "name";
        sortDirection = "asc";
        applyFilters();
    }

    function sortBy(field) {
        if (sortField === field) {
            sortDirection = sortDirection === "asc" ? "desc" : "asc";
        } else {
            sortField = field;
            sortDirection = "asc";
        }
        applyFilters();
    }

    function confirmDelete(project) {
        deleteModal = { show: true, project };
    }

    function deleteProject() {
        if (deleteModal.project) {
            router.delete(`/projects/delete/${deleteModal.project.id}/`, {
                onSuccess: () => {
                    deleteModal = { show: false, project: null };
                },
            });
        }
    }

    function getStatusBadge(st) {
        const badges = {
            planning: "bg-info bg-opacity-10 text-info",
            active: "bg-success bg-opacity-10 text-success",
            on_hold: "bg-warning bg-opacity-10 text-warning",
            completed: "bg-secondary bg-opacity-10 text-secondary",
            cancelled: "bg-danger bg-opacity-10 text-danger",
        };
        return badges[st] || "bg-secondary bg-opacity-10 text-secondary";
    }

    function getProgressColor(percentage) {
        if (percentage < 30) return "bg-danger";
        if (percentage < 70) return "bg-warning";
        return "bg-success";
    }

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

<AdminLayout>
    <!-- Header -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-semibold mb-1">
                        <i class="bi bi-kanban"></i>
                        Gestión de Proyectos
                    </h5>
                    <p class="text-muted mb-0">
                        Administra los proyectos de la organización
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <Link
                        href="/projects/create"
                        class="btn btn-primary d-flex align-items-center gap-2"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Nuevo Proyecto
                    </Link>
                    <a
                        href="/projects/export"
                        class="btn btn-outline-success d-flex align-items-center gap-2"
                    >
                        <i class="bi bi-download"></i> Exportar
                    </a>
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick={() => (showImportModal = true)}
                    >
                        <i class="bi bi-upload"></i>
                        Importar
                    </button>
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
                        placeholder="Nombre o código..."
                        bind:value={search}
                    />
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="status">Estado</label>
                    <select
                        id="status"
                        class="form-select"
                        bind:value={status}
                        onchange={applyFilters}
                    >
                        <option value="all">Todos</option>
                        {#each Object.entries(statuses) as [key, label]}
                            <option value={key}>{label}</option>
                        {/each}
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
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button
                        class="btn btn-outline-secondary w-100"
                        onclick={clearFilters}
                    >
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Proyectos -->
    <div class="row g-3 mt-1">
        {#if projects.data.length > 0}
            {#each projects.data as project}
                <div class="col-lg-4 col-md-6">
                    <div class="project-card">
                        <!-- Header -->
                        <div class="project-card-header">
                            <div
                                class="d-flex justify-content-between align-items-start mb-2"
                            >
                                <div class="flex-grow-1">
                                    <h5 class="project-title mb-1">
                                        <Link
                                            href={`/projects/show/${project.id}`}
                                        >
                                            {project.name}
                                        </Link>
                                    </h5>
                                    <span class="project-code"
                                        >{project.code}</span
                                    >
                                </div>
                                <span
                                    class="badge {getStatusBadge(
                                        project.status,
                                    )}"
                                >
                                    {statuses[project.status]}
                                </span>
                            </div>

                            {#if project.description}
                                <p class="project-description">
                                    {project.description.substring(
                                        0,
                                        100,
                                    )}{project.description.length > 100
                                        ? "..."
                                        : ""}
                                </p>
                            {/if}
                        </div>

                        <!-- Info -->
                        <div class="project-card-body">
                            {#if project.project_manager}
                                <div class="info-item">
                                    <i class="bi bi-person-circle text-primary"
                                    ></i>
                                    <div class="info-content">
                                        <small class="text-muted">Manager</small
                                        >
                                        <span class="fw-medium">
                                            {project.project_manager.first_name}
                                            {project.project_manager.last_name}
                                        </span>
                                    </div>
                                </div>
                            {/if}

                            <div class="info-item">
                                <i class="bi bi-people-fill text-info"></i>
                                <div class="info-content">
                                    <small class="text-muted">Equipo</small>
                                    <span class="fw-medium">
                                        {project.project_assignments?.filter(
                                            (a) => a.is_active,
                                        ).length || 0} miembros
                                    </span>
                                </div>
                            </div>

                            {#if project.budget}
                                <div class="info-item">
                                    <i class="bi bi-cash-stack text-success"
                                    ></i>
                                    <div class="info-content">
                                        <small class="text-muted"
                                            >Presupuesto</small
                                        >
                                        <span class="fw-medium">
                                            ${parseFloat(
                                                project.budget,
                                            ).toLocaleString("es-CO")}
                                        </span>
                                    </div>
                                </div>
                            {/if}

                            {#if project.end_date}
                                <div class="progress-section">
                                    <div
                                        class="d-flex justify-content-between mb-1"
                                    >
                                        <small class="text-muted"
                                            >Progreso</small
                                        >
                                        <small class="fw-bold">
                                            {project.progress_percentage || 0}%
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div
                                            class="progress-bar {getProgressColor(
                                                project.progress_percentage ||
                                                    0,
                                            )}"
                                            style="width: {project.progress_percentage ||
                                                0}%"
                                        ></div>
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <!-- Footer -->
                        <div class="project-card-footer">
                            <div class="btn-group w-100" role="group">
                                <Link
                                    href={`/projects/show/${project.id}`}
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-eye"></i>
                                </Link>
                                <Link
                                    href={`/projects/edit/${project.id}`}
                                    class="btn btn-sm btn-outline-warning"
                                >
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick={() => confirmDelete(project)}
                                    aria-label="delete"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            {/each}
        {:else}
            <div class="col-12">
                <div class="text-center py-5">
                    <i
                        class="bi bi-kanban"
                        style="font-size: 4rem; color: #adb5bd;"
                    ></i>
                    <p class="text-muted mt-3">No se encontraron proyectos</p>
                </div>
            </div>
        {/if}
    </div>

    <!-- Paginación -->
    {#if projects.data.length > 0}
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Mostrando {projects.from} a {projects.to} de {projects.total}
                        registros
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            {#each projects.links as link}
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
                                            preserveScroll
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
            </div>
        </div>
    {/if}

    <!-- Modal de eliminación -->
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
                                (deleteModal = { show: false, project: null })}
                            aria-label="Cerrar"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            ¿Estás seguro de que deseas eliminar el proyecto
                            <strong>{deleteModal.project?.name}</strong>?
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
                                (deleteModal = { show: false, project: null })}
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick={deleteProject}
                        >
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- Modal de Importación -->
    <ImportModal
        show={showImportModal}
        onClose={() => (showImportModal = false)}
        importUrl="/projects/import"
        templateFile="projects_template.xlsx"
    />
</AdminLayout>

<style>
    .project-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .project-card:hover {
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
        transform: translateY(-4px);
    }

    .project-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .project-title {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
    }

    .project-title a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .project-title a:hover {
        color: #667eea;
    }

    .project-code {
        font-size: 0.75rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .project-description {
        color: #6c757d;
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.5;
    }

    .project-card-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .info-item i {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .info-content small {
        font-size: 0.75rem;
        display: block;
    }

    .info-content span {
        font-size: 0.875rem;
    }

    .progress-section {
        margin-top: auto;
    }

    .project-card-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
</style>
