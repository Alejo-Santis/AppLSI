<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";

    let { position, stats, levels } = $props();

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

<AdminLayout title={`Puesto: ${position.title}`}>
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="card-title fw-semibold mb-0">
                                {position.title}
                            </h5>
                            <span class="badge {getLevelBadgeClass(position.level)}">
                                {levels[position.level]}
                            </span>
                            {#if position.is_active}
                                <span class="badge bg-light-success text-success">
                                    Activo
                                </span>
                            {:else}
                                <span class="badge bg-light-danger text-danger">
                                    Inactivo
                                </span>
                            {/if}
                        </div>
                        {#if position.description}
                            <p class="text-muted mb-0">{position.description}</p>
                        {/if}
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href={`/positions/${position.id}/edit`}
                            class="btn btn-primary"
                        >
                            <i class="bi bi-pencil"></i>
                            Editar
                        </Link>
                        <Link href="/positions" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Estadísticas -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-people fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">{stats.total_employees}</h4>
                                <p class="text-muted mb-0">Total Empleados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-person-check fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">{stats.active_employees}</h4>
                                <p class="text-muted mb-0">Activos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    ${(stats.average_salary || 0).toLocaleString('es-CO', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    })}
                                </h4>
                                <p class="text-muted mb-0">Salario Promedio</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-warning text-warning d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold" style="font-size: 0.9rem;">
                                    ${(stats.min_employee_salary || 0).toLocaleString('es-CO', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    })}
                                </h6>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Mín - Máx</p>
                                <h6 class="mb-0 fw-semibold" style="font-size: 0.9rem;">
                                    ${(stats.max_employee_salary || 0).toLocaleString('es-CO', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    })}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Información General -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Información General</h5>

                        <div class="mb-3">
                            <small class="text-muted d-block">Título</small>
                            <span class="fw-semibold">{position.title}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Nivel</small>
                            <span class="badge {getLevelBadgeClass(position.level)} mt-1">
                                {levels[position.level]}
                            </span>
                        </div>

                        {#if position.min_salary && position.max_salary}
                            <div class="mb-3">
                                <small class="text-muted d-block">Rango Salarial Definido</small>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Mínimo:</small>
                                        <span class="fw-semibold">
                                            ${parseFloat(position.min_salary).toLocaleString('es-CO')}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small>Máximo:</small>
                                        <span class="fw-semibold">
                                            ${parseFloat(position.max_salary).toLocaleString('es-CO')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        {/if}

                        <div class="mb-3">
                            <small class="text-muted d-block">Estado</small>
                            {#if position.is_active}
                                <span class="badge bg-light-success text-success mt-1">
                                    Activo
                                </span>
                            {:else}
                                <span class="badge bg-light-danger text-danger mt-1">
                                    Inactivo
                                </span>
                            {/if}
                        </div>

                        <hr>

                        <div class="mb-2">
                            <small class="text-muted d-block">Fecha de Creación</small>
                            <span>
                                {new Date(position.created_at).toLocaleDateString('es-CO', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}
                            </span>
                        </div>

                        <div>
                            <small class="text-muted d-block">Última Actualización</small>
                            <span>
                                {new Date(position.updated_at).toLocaleDateString('es-CO', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empleados con este Puesto -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">
                                Empleados ({position.employees?.length || 0})
                            </h5>
                            <Link
                                href="/employees/create"
                                class="btn btn-sm btn-primary"
                            >
                                <i class="bi bi-plus"></i>
                                Agregar Empleado
                            </Link>
                        </div>

                        {#if position.employees && position.employees.length > 0}
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Empleado</th>
                                            <th>Departamento</th>
                                            <th class="text-end">Salario</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {#each position.employees as employee}
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div
                                                            class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px;"
                                                        >
                                                            {employee.first_name.charAt(0)}{employee.last_name.charAt(0)}
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">
                                                                {employee.first_name} {employee.last_name}
                                                            </h6>
                                                            <small class="text-muted">{employee.email}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {#if employee.department}
                                                        <span class="badge bg-light-info text-info">
                                                            {employee.department.name}
                                                        </span>
                                                    {:else}
                                                        <span class="text-muted">N/A</span>
                                                    {/if}
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-semibold">
                                                        ${parseFloat(employee.salary).toLocaleString('es-CO')}
                                                    </span>
                                                </td>
                                                <td>
                                                    {#if employee.status === 'active'}
                                                        <span class="badge bg-light-success text-success">
                                                            Activo
                                                        </span>
                                                    {:else if employee.status === 'inactive'}
                                                        <span class="badge bg-light-danger text-danger">
                                                            Inactivo
                                                        </span>
                                                    {:else if employee.status === 'on_leave'}
                                                        <span class="badge bg-light-warning text-warning">
                                                            En Licencia
                                                        </span>
                                                    {:else}
                                                        <span class="badge bg-light-secondary text-secondary">
                                                            {employee.status}
                                                        </span>
                                                    {/if}
                                                </td>
                                                <td class="text-center">
                                                    <Link
                                                        href={`/employees/${employee.id}`}
                                                        class="btn btn-sm btn-light-primary"
                                                        title="Ver detalle"
                                                    >
                                                        <i class="bi bi-eye"></i>
                                                    </Link>
                                                </td>
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        {:else}
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-slash fs-1"></i>
                                    <p class="mt-2">
                                        No hay empleados con este puesto
                                    </p>
                                    <Link
                                        href="/employees/create"
                                        class="btn btn-sm btn-primary mt-2"
                                    >
                                        <i class="bi bi-plus"></i>
                                        Agregar Primer Empleado
                                    </Link>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- Distribución por Departamentos -->
                {#if stats.departments_distribution && Object.keys(stats.departments_distribution).length > 0}
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                Distribución por Departamentos
                            </h5>
                            <div class="row">
                                {#each Object.entries(stats.departments_distribution) as [department, count]}
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">{department || 'Sin departamento'}</span>
                                            <span class="badge bg-light-primary text-primary">
                                                {count} empleado{count !== 1 ? 's' : ''}
                                            </span>
                                        </div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div
                                                class="progress-bar bg-primary"
                                                role="progressbar"
                                                style="width: {(count / stats.total_employees) * 100}%"
                                            ></div>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</AdminLayout>
