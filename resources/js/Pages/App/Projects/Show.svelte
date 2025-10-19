<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link, router, useForm } from "@inertiajs/svelte";
    import Swal from "sweetalert2";

    let { project, stats, availableEmployees } = $props();

    let showAssignModal = $state(false);
    let assignForm = useForm({
        employee_id: "",
        role: "",
        assigned_date: new Date().toISOString().split("T")[0],
        allocation_percentage: 100,
    });

    function getStatusBadge(status) {
        const badges = {
            planning: "bg-info bg-opacity-10 text-info",
            active: "bg-success bg-opacity-10 text-success",
            on_hold: "bg-warning bg-opacity-10 text-warning",
            completed: "bg-secondary bg-opacity-10 text-secondary",
            cancelled: "bg-danger bg-opacity-10 text-danger",
        };
        return badges[status] || "bg-secondary bg-opacity-10 text-secondary";
    }

    function getStatusLabel(status) {
        const labels = {
            planning: "En Planeación",
            active: "Activo",
            on_hold: "En Pausa",
            completed: "Completado",
            cancelled: "Cancelado",
        };
        return labels[status] || status;
    }

    function getProgressColor(percentage) {
        if (percentage < 30) return "bg-danger";
        if (percentage < 70) return "bg-warning";
        return "bg-success";
    }

    function assignEmployee(e) {
        e.preventDefault();
        $assignForm.post(`/projects/${project.id}/assign`, {
            onSuccess: () => {
                showAssignModal = false;
                $assignForm.reset();
            },
        });
    }

    function removeEmployee(assignmentId) {
        Swal.fire({
            title: "¿Remover empleado del proyecto?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, remover",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(
                    `/projects/${project.id}/assignments/${assignmentId}/remove`,
                    {
                        onSuccess: () => {
                            Swal.fire({
                                title: "Empleado removido",
                                text: "El empleado ha sido eliminado del proyecto.",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        },
                        onError: () => {
                            Swal.fire({
                                title: "Error",
                                text: "No se pudo remover el empleado. Intenta nuevamente.",
                                icon: "error",
                            });
                        },
                    },
                );
            }
        });
    }
</script>

<AdminLayout>
    <!-- Header -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="project-icon">
                            <i class="bi bi-kanban-fill"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="mb-0">{project.name}</h4>
                                <span
                                    class="badge {getStatusBadge(
                                        project.status,
                                    )}"
                                >
                                    {getStatusLabel(project.status)}
                                </span>
                            </div>
                            <span class="project-code">{project.code}</span>
                        </div>
                    </div>

                    {#if project.description}
                        <p class="text-muted mb-0">{project.description}</p>
                    {/if}
                </div>

                <div class="d-flex gap-2">
                    <Link
                        href={`/projects/edit/${project.id}`}
                        class="btn btn-primary"
                    >
                        <i class="bi bi-pencil"></i>
                        Editar
                    </Link>
                    <Link href="/projects" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-people-fill text-primary"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{stats.active_employees}</h3>
                    <p class="stat-label">Empleados Activos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-calendar-check text-info"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">
                        {stats.days_remaining >= 0 ? stats.days_remaining : 0}
                    </h3>
                    <p class="stat-label">Días Restantes</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-graph-up-arrow text-success"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{stats.progress_percentage}%</h3>
                    <p class="stat-label">Progreso</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-cash-stack text-warning"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number fs-5">
                        {#if project.budget}
                            ${parseFloat(project.budget).toLocaleString(
                                "es-CO",
                            )}
                        {:else}
                            N/A
                        {/if}
                    </h3>
                    <p class="stat-label">Presupuesto</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información y Equipo -->
    <div class="row g-3 mt-1">
        <!-- Información General -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-info-circle"></i>
                        Información General
                    </h5>

                    <div class="info-item">
                        <small class="text-muted">Project Manager</small>
                        {#if project.project_manager}
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="avatar-sm">
                                    <span class="avatar-text">
                                        {project.project_manager.first_name.charAt(
                                            0,
                                        )}{project.project_manager.last_name.charAt(
                                            0,
                                        )}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-medium">
                                        {project.project_manager.first_name}
                                        {project.project_manager.last_name}
                                    </div>
                                    <small class="text-muted">
                                        {project.project_manager.email}
                                    </small>
                                </div>
                            </div>
                        {:else}
                            <span class="text-muted">Sin asignar</span>
                        {/if}
                    </div>

                    <div class="info-item">
                        <small class="text-muted">Fecha de Inicio</small>
                        <div class="fw-medium">
                            {new Date(project.start_date).toLocaleDateString(
                                "es-CO",
                                {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                },
                            )}
                        </div>
                    </div>

                    {#if project.end_date}
                        <div class="info-item">
                            <small class="text-muted"
                                >Fecha de Finalización</small
                            >
                            <div class="fw-medium">
                                {new Date(project.end_date).toLocaleDateString(
                                    "es-CO",
                                    {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    },
                                )}
                            </div>
                        </div>
                    {/if}

                    {#if project.end_date}
                        <div class="info-item">
                            <small class="text-muted">Progreso</small>
                            <div class="progress mt-2" style="height: 12px;">
                                <div
                                    class="progress-bar {getProgressColor(
                                        stats.progress_percentage,
                                    )}"
                                    style="width: {stats.progress_percentage}%"
                                >
                                    <small class="fw-bold"
                                        >{stats.progress_percentage}%</small
                                    >
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <!-- Equipo del Proyecto -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div
                        class="d-flex justify-content-between align-items-center mb-4"
                    >
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people-fill"></i>
                            Equipo del Proyecto
                        </h5>
                        <button
                            class="btn btn-sm btn-primary"
                            onclick={() => (showAssignModal = true)}
                        >
                            <i class="bi bi-person-plus"></i>
                            Asignar Empleado
                        </button>
                    </div>

                    {#if project.project_assignments && project.project_assignments.length > 0}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Empleado</th>
                                        <th>Rol</th>
                                        <th>Asignado</th>
                                        <th>Dedicación</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {#each project.project_assignments as assignment}
                                        <tr>
                                            <td>
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                >
                                                    <div class="avatar-sm">
                                                        <span
                                                            class="avatar-text"
                                                        >
                                                            {assignment.employee.first_name.charAt(
                                                                0,
                                                            )}{assignment.employee.last_name.charAt(
                                                                0,
                                                            )}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">
                                                            {assignment.employee
                                                                .first_name}
                                                            {assignment.employee
                                                                .last_name}
                                                        </div>
                                                        <small
                                                            class="text-muted"
                                                        >
                                                            {assignment.employee
                                                                .position
                                                                ?.title ||
                                                                "N/A"}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{assignment.role || "N/A"}</td>
                                            <td>
                                                <small>
                                                    {new Date(
                                                        assignment.assigned_date,
                                                    ).toLocaleDateString(
                                                        "es-CO",
                                                    )}
                                                </small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info"
                                                >
                                                    {assignment.allocation_percentage}%
                                                </span>
                                            </td>
                                            <td>
                                                {#if assignment.is_active}
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success"
                                                    >
                                                        Activo
                                                    </span>
                                                {:else}
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary"
                                                    >
                                                        Finalizado
                                                    </span>
                                                {/if}
                                            </td>
                                            <td class="text-center">
                                                <div
                                                    class="btn-group btn-group-sm"
                                                >
                                                    <Link
                                                        href={`/employees/show/${assignment.employee.id}`}
                                                        class="btn btn-outline-primary"
                                                    >
                                                        <i class="bi bi-eye"
                                                        ></i>
                                                    </Link>
                                                    {#if assignment.is_active}
                                                        <button
                                                            class="btn btn-outline-danger"
                                                            aria-label="remove employee"
                                                            onclick={() =>
                                                                removeEmployee(
                                                                    assignment.id,
                                                                )}
                                                        >
                                                            <i
                                                                class="bi bi-x-lg"
                                                            ></i>
                                                        </button>
                                                    {/if}
                                                </div>
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                        </div>
                    {:else}
                        <div class="text-center py-5">
                            <i
                                class="bi bi-people"
                                style="font-size: 3rem; color: #adb5bd;"
                            ></i>
                            <p class="text-muted mt-3">
                                No hay empleados asignados a este proyecto
                            </p>
                            <button
                                class="btn btn-primary"
                                onclick={() => (showAssignModal = true)}
                            >
                                <i class="bi bi-person-plus"></i>
                                Asignar Primer Empleado
                            </button>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Empleado -->
    {#if showAssignModal}
        <div
            class="modal fade show d-block"
            style="background-color: rgba(0,0,0,0.5);"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form onsubmit={assignEmployee}>
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Asignar Empleado al Proyecto
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                aria-label="close"
                                onclick={() => (showAssignModal = false)}
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">
                                    Empleado <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select"
                                    id="employee_id"
                                    bind:value={$assignForm.employee_id}
                                    required
                                >
                                    <option value="">Seleccionar...</option>
                                    {#each availableEmployees as employee}
                                        <option value={employee.id}>
                                            {employee.name} - {employee.email}
                                        </option>
                                    {/each}
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="role"
                                    bind:value={$assignForm.role}
                                    placeholder="Ej: Desarrollador Frontend"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="assigned_date" class="form-label">
                                    Fecha de Asignación <span
                                        class="text-danger">*</span
                                    >
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="assigned_date"
                                    bind:value={$assignForm.assigned_date}
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label
                                    for="allocation_percentage"
                                    class="form-label"
                                >
                                    Dedicación (%) <span class="text-danger"
                                        >*</span
                                    >
                                </label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="allocation_percentage"
                                    bind:value={
                                        $assignForm.allocation_percentage
                                    }
                                    min="1"
                                    max="100"
                                    required
                                />
                                <small class="text-muted">
                                    Porcentaje de tiempo dedicado al proyecto
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-light"
                                onclick={() => (showAssignModal = false)}
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                disabled={$assignForm.processing}
                            >
                                {#if $assignForm.processing}
                                    <span
                                        class="spinner-border spinner-border-sm me-2"
                                    ></span>
                                    Asignando...
                                {:else}
                                    <i class="bi bi-check-lg"></i>
                                    Asignar
                                {/if}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    {/if}
</AdminLayout>

<style>
    .project-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }

    .project-code {
        font-size: 0.875rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    .stat-label {
        margin: 0;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .info-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f3f5;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-text {
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }
</style>
