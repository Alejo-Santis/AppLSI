<script>
    import { Link } from "@inertiajs/svelte";

    let { employee } = $props();

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
</script>

<div class="projects-tab">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-diagram-3 text-primary"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">
                        {employee.project_assignments?.filter(
                            (a) => a.is_active,
                        ).length || 0}
                    </h3>
                    <p class="stat-label">Proyectos Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">
                        {employee.project_assignments?.filter(
                            (a) => a.project?.status === "completed",
                        ).length || 0}
                    </h3>
                    <p class="stat-label">Completados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-percent text-info"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">
                        {Math.round(
                            employee.project_assignments
                                ?.filter((a) => a.is_active)
                                .reduce(
                                    (sum, a) => sum + a.allocation_percentage,
                                    0,
                                ) || 0,
                        )}%
                    </h3>
                    <p class="stat-label">Dedicación Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Proyectos Activos -->
    {#if employee.project_assignments && employee.project_assignments.length > 0}
        <div class="mb-4">
            <h5 class="section-title">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                Proyectos Activos
            </h5>

            <div class="row g-3">
                {#each employee.project_assignments.filter((a) => a.is_active) as assignment}
                    <div class="col-lg-6">
                        <div class="project-card">
                            <!-- Header del proyecto -->
                            <div class="project-header">
                                <div
                                    class="d-flex justify-content-between align-items-start mb-2"
                                >
                                    <div>
                                        <h6 class="project-name mb-1">
                                            <Link
                                                href={`/projects/${assignment.project.id}`}
                                                class="project-link"
                                            >
                                                {assignment.project.name}
                                            </Link>
                                        </h6>
                                        <span class="project-code">
                                            {assignment.project.code}
                                        </span>
                                    </div>
                                    <span
                                        class="badge {getStatusBadge(
                                            assignment.project.status,
                                        )}"
                                    >
                                        {getStatusLabel(
                                            assignment.project.status,
                                        )}
                                    </span>
                                </div>

                                {#if assignment.role}
                                    <div class="project-role">
                                        <i class="bi bi-person-badge"></i>
                                        {assignment.role}
                                    </div>
                                {/if}
                            </div>

                            <!-- Información del assignment -->
                            <div class="project-info">
                                <div class="info-row">
                                    <div class="info-item">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i> Asignado
                                        </small>
                                        <span>
                                            {new Date(
                                                assignment.assigned_date,
                                            ).toLocaleDateString("es-CO")}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <small class="text-muted">
                                            <i class="bi bi-speedometer2"></i>
                                            Dedicación
                                        </small>
                                        <span class="fw-bold">
                                            {assignment.allocation_percentage}%
                                        </span>
                                    </div>
                                </div>

                                {#if assignment.project.end_date}
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">
                                            Progreso del proyecto
                                        </small>
                                        <div
                                            class="progress"
                                            style="height: 8px;"
                                        >
                                            <div
                                                class="progress-bar {getProgressColor(
                                                    assignment.project
                                                        .progress_percentage ||
                                                        0,
                                                )}"
                                                role="progressbar"
                                                style="width: {assignment
                                                    .project
                                                    .progress_percentage || 0}%"
                                            ></div>
                                        </div>
                                        <small class="text-muted">
                                            {assignment.project
                                                .progress_percentage || 0}%
                                            completado
                                        </small>
                                    </div>
                                {/if}
                            </div>

                            <!-- Footer con acción -->
                            <div class="project-footer">
                                <Link
                                    href={`/projects/show/${assignment.project.id}`}
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-arrow-right-circle"></i>
                                    Ver detalles
                                </Link>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        </div>

        <!-- Historial de Proyectos -->
        {#if employee.project_assignments.filter((a) => !a.is_active).length > 0}
            <div class="mt-4">
                <h5 class="section-title">
                    <i class="bi bi-clock-history text-muted"></i>
                    Historial de Proyectos
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Proyecto</th>
                                <th>Rol</th>
                                <th>Periodo</th>
                                <th>Dedicación</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#each employee.project_assignments.filter((a) => !a.is_active) as assignment}
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 fs-6">
                                                {assignment.project.name}
                                            </h6>
                                            <small class="text-muted">
                                                {assignment.project.code}
                                            </small>
                                        </div>
                                    </td>
                                    <td>{assignment.role || "N/A"}</td>
                                    <td>
                                        <small>
                                            {new Date(
                                                assignment.assigned_date,
                                            ).toLocaleDateString("es-CO")}
                                            {#if assignment.end_date}
                                                <br />
                                                <i class="bi bi-arrow-right"
                                                ></i>
                                                {new Date(
                                                    assignment.end_date,
                                                ).toLocaleDateString("es-CO")}
                                            {/if}
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
                                        <span
                                            class="badge {getStatusBadge(
                                                assignment.project.status,
                                            )}"
                                        >
                                            {getStatusLabel(
                                                assignment.project.status,
                                            )}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <Link
                                            href={`/projects/${assignment.project.id}`}
                                            class="btn btn-sm btn-ghost-primary"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </Link>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
    {:else}
        <!-- Estado vacío -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-kanban"></i>
            </div>
            <h5 class="empty-title">Sin proyectos asignados</h5>
            <p class="empty-description">
                Este empleado aún no ha sido asignado a ningún proyecto.
            </p>
            <Link href="/projects" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Ver proyectos disponibles
            </Link>
        </div>
    {/if}
</div>

<style>
    /* Estadísticas */
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

    .stat-content {
        flex: 1;
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

    /* Section Title */
    .section-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Project Card */
    .project-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
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

    .project-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .project-name {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .project-link {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .project-link:hover {
        color: #667eea;
    }

    .project-code {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 500;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .project-role {
        font-size: 0.875rem;
        color: #667eea;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .project-info {
        padding: 1.25rem;
        flex: 1;
    }

    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-item small {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
    }

    .info-item span {
        font-size: 0.875rem;
        color: #2c3e50;
    }

    .project-footer {
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .empty-title {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    /* Table refinements */
    .table thead {
        border-bottom: 2px solid #667eea;
    }

    .btn-ghost-primary {
        color: #667eea;
        background: transparent;
        border: none;
        padding: 0.25rem 0.5rem;
    }

    .btn-ghost-primary:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }
</style>
