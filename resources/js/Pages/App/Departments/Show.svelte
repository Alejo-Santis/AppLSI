<script>
    import AdminLayout from "../../../Layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";

    let { department, stats } = $props();
</script>

<AdminLayout title={`Departamento: ${department.name}`}>
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="card-title fw-semibold mb-0">
                                <i class="bi bi-buildings"></i>
                                {department.name}
                            </h5>
                            {#if department.is_active}
                                <span
                                    class="badge bg-light-success text-success"
                                >
                                    Activo
                                </span>
                            {:else}
                                <span class="badge bg-light-danger text-danger">
                                    Inactivo
                                </span>
                            {/if}
                        </div>
                        <p class="text-muted mb-2">
                            <span class="badge bg-light-primary text-primary">
                                {department.code}
                            </span>
                        </p>
                        {#if department.description}
                            <p class="text-muted mb-0">
                                {department.description}
                            </p>
                        {/if}
                    </div>
                    <div class="d-flex gap-2">
                        <Link
                            href={`/departments/${department.id}/edit`}
                            class="btn btn-primary"
                        >
                            <i class="bi bi-pencil"></i>
                            Editar
                        </Link>
                        <Link
                            href="/departments"
                            class="btn btn-outline-secondary"
                        >
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
                                <i class="bi bi-person-circle fs-6"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.total_employees}
                                </h4>
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
                                <i class="bi bi-person-check fs-6"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.active_employees}
                                </h4>
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
                                <i class="bi bi-cash fs-6"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    ${(stats.total_salary || 0).toLocaleString(
                                        "es-CO",
                                        {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0,
                                        },
                                    )}
                                </h4>
                                <p class="text-muted mb-0">Masa Salarial</p>
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
                                <i class="bi bi-bar-chart fs-6"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    ${(
                                        stats.average_salary || 0
                                    ).toLocaleString("es-CO", {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0,
                                    })}
                                </h4>
                                <p class="text-muted mb-0">Salario Promedio</p>
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
                            <small class="text-muted d-block">Código</small>
                            <span class="fw-semibold">{department.code}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block"
                                >Manager / Jefe</small
                            >
                            {#if department.manager}
                                <div
                                    class="d-flex align-items-center gap-2 mt-2"
                                >
                                    <div
                                        class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;"
                                    >
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-semibold">
                                            {department.manager.first_name}
                                            {department.manager.last_name}
                                        </p>
                                        <small class="text-muted">
                                            {department.manager.email}
                                        </small>
                                    </div>
                                </div>
                            {:else}
                                <span class="text-muted">Sin asignar</span>
                            {/if}
                        </div>

                        {#if department.budget}
                            <div class="mb-3">
                                <small class="text-muted d-block"
                                    >Presupuesto Anual</small
                                >
                                <span class="fw-semibold">
                                    ${parseFloat(
                                        department.budget,
                                    ).toLocaleString("es-CO")}
                                </span>
                            </div>
                        {/if}

                        <div class="mb-3">
                            <small class="text-muted d-block">Estado</small>
                            {#if department.is_active}
                                <span
                                    class="badge bg-light-success text-success mt-1"
                                >
                                    Activo
                                </span>
                            {:else}
                                <span
                                    class="badge bg-light-danger text-danger mt-1"
                                >
                                    Inactivo
                                </span>
                            {/if}
                        </div>

                        <hr />

                        <div class="mb-2">
                            <small class="text-muted d-block"
                                >Fecha de Creación</small
                            >
                            <span>
                                {new Date(
                                    department.created_at,
                                ).toLocaleDateString("es-CO", {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                })}
                            </span>
                        </div>

                        <div>
                            <small class="text-muted d-block"
                                >Última Actualización</small
                            >
                            <span>
                                {new Date(
                                    department.updated_at,
                                ).toLocaleDateString("es-CO", {
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                })}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empleados del Departamento -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4"
                        >
                            <h5 class="card-title mb-0">
                                Empleados ({department.employees?.length || 0})
                            </h5>
                            <Link
                                href="/employees/create"
                                class="btn btn-sm btn-primary"
                            >
                                <i class="bi bi-plus"></i>
                                Agregar Empleado
                            </Link>
                        </div>

                        {#if department.employees && department.employees.length > 0}
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Empleado</th>
                                            <th>Puesto</th>
                                            <th>Email</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {#each department.employees as employee}
                                            <tr>
                                                <td>
                                                    <div
                                                        class="d-flex align-items-center gap-2"
                                                    >
                                                        <div
                                                            class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px;"
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
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {#if employee.position}
                                                        <span
                                                            class="badge bg-light-info text-info"
                                                        >
                                                            {employee.position
                                                                .title}
                                                        </span>
                                                    {:else}
                                                        <span class="text-muted"
                                                            >N/A</span
                                                        >
                                                    {/if}
                                                </td>
                                                <td>
                                                    <small
                                                        >{employee.email}</small
                                                    >
                                                </td>
                                                <td>
                                                    {#if employee.status === "active"}
                                                        <span
                                                            class="badge bg-light-success text-success"
                                                        >
                                                            Activo
                                                        </span>
                                                    {:else if employee.status === "inactive"}
                                                        <span
                                                            class="badge bg-light-danger text-danger"
                                                        >
                                                            Inactivo
                                                        </span>
                                                    {:else if employee.status === "on_leave"}
                                                        <span
                                                            class="badge bg-light-warning text-warning"
                                                        >
                                                            En Licencia
                                                        </span>
                                                    {:else}
                                                        <span
                                                            class="badge bg-light-secondary text-secondary"
                                                        >
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
                                                        <i class="ti ti-eye"
                                                        ></i>
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
                                    <i class="ti ti-users-off fs-9"></i>
                                    <p class="mt-2">
                                        Este departamento no tiene empleados
                                        asignados
                                    </p>
                                    <Link
                                        href="/employees/create"
                                        class="btn btn-sm btn-primary mt-2"
                                    >
                                        <i class="ti ti-plus"></i>
                                        Agregar Primer Empleado
                                    </Link>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- Distribución por Puestos -->
                {#if stats.positions_distribution && Object.keys(stats.positions_distribution).length > 0}
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                Distribución por Puestos
                            </h5>
                            <div class="row">
                                {#each Object.entries(stats.positions_distribution) as [position, count]}
                                    <div class="col-md-6 mb-3">
                                        <div
                                            class="d-flex justify-content-between align-items-center"
                                        >
                                            <span class="text-muted"
                                                >{position ||
                                                    "Sin puesto"}</span
                                            >
                                            <span
                                                class="badge bg-light-primary text-primary"
                                            >
                                                {count} empleado{count !== 1
                                                    ? "s"
                                                    : ""}
                                            </span>
                                        </div>
                                        <div
                                            class="progress mt-2"
                                            style="height: 8px;"
                                        >
                                            <div
                                                class="progress-bar bg-primary"
                                                role="progressbar"
                                                style="width: {(count /
                                                    stats.total_employees) *
                                                    100}%"
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
