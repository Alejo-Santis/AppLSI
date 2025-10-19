<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import DocumentsTab from "@components/DocumentsTab.svelte";
    import EmergencyContactsTab from "@components/EmergencyContactsTab.svelte";
    import SalaryHistoryTab from "@components/SalaryHistoryTab.svelte";
    import { Link } from "@inertiajs/svelte";
    import ProjectsTab from "../../../Components/ProjectsTab.svelte";

    let { employee, stats } = $props();

    let activeTab = $state("general");

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

<AdminLayout title={`${employee.first_name} ${employee.last_name}`}>
    <div class="container-fluid">
        <!-- Header -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-3 align-items-start">
                        <!-- Foto -->
                        <div>
                            {#if employee.photo_url}
                                <img
                                    src={`/storage/${employee.photo_url}`}
                                    alt={`${employee.first_name} ${employee.last_name}`}
                                    class="rounded"
                                    style="width: 100px; height: 100px; object-fit: cover;"
                                />
                            {:else}
                                <div
                                    class="rounded bg-light-primary text-primary d-flex align-items-center justify-content-center text-uppercase fw-bold"
                                    style="width: 100px; height: 100px; font-size: 32px;"
                                >
                                    {employee.first_name.charAt(
                                        0,
                                    )}{employee.last_name.charAt(0)}
                                </div>
                            {/if}
                        </div>

                        <!-- Info -->
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="card-title fw-semibold mb-0">
                                    {employee.first_name}
                                    {employee.last_name}
                                </h5>
                                <span
                                    class="badge {getStatusBadge(
                                        employee.status,
                                    )}"
                                >
                                    {getStatusLabel(employee.status)}
                                </span>
                            </div>

                            <div class="mb-2">
                                {#if employee.position}
                                    <span
                                        class="badge bg-light-info text-info me-2"
                                    >
                                        <i class="bi bi-briefcase"></i>
                                        {employee.position.title}
                                    </span>
                                {/if}
                                {#if employee.department}
                                    <span
                                        class="badge bg-light-secondary text-secondary"
                                    >
                                        <i class="bi bi-building"></i>
                                        {employee.department.name}
                                    </span>
                                {/if}
                            </div>

                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope"></i>
                                {employee.email}
                            </p>
                            {#if employee.phone}
                                <p class="text-muted mb-0">
                                    <i class="bi bi-telephone"></i>
                                    {employee.phone}
                                </p>
                            {/if}
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <Link
                            href={`/employees/edit/${employee.id}`}
                            class="btn btn-primary"
                        >
                            <i class="bi bi-pencil"></i>
                            Editar
                        </Link>
                        <Link
                            href="/employees"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.years_of_service}
                                </h4>
                                <p class="text-muted mb-0">
                                    Año{stats.years_of_service !== 1 ? "s" : ""}
                                    de Servicio
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-folder fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.total_documents}
                                </h4>
                                <p class="text-muted mb-0">Documentos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-diagram-3 fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.active_projects}
                                </h4>
                                <p class="text-muted mb-0">Proyectos Activos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-warning text-warning d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 fw-semibold">
                                    {stats.salary_changes}
                                </h4>
                                <p class="text-muted mb-0">
                                    Cambios Salariales
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestañas -->
        <div class="card mt-3">
            <div class="card-body">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {activeTab === 'general'
                                ? 'active'
                                : ''}"
                            onclick={() => (activeTab = "general")}
                        >
                            <i class="bi bi-info-circle"></i> General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {activeTab === 'documents'
                                ? 'active'
                                : ''}"
                            onclick={() => (activeTab = "documents")}
                        >
                            <i class="bi bi-folder"></i> Documentos
                            <span
                                class="badge bg-light-secondary text-secondary ms-1"
                            >
                                {employee.documents?.length || 0}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {activeTab === 'contacts'
                                ? 'active'
                                : ''}"
                            onclick={() => (activeTab = "contacts")}
                        >
                            <i class="bi bi-person-lines-fill"></i> Contactos de
                            Emergencia
                            <span
                                class="badge bg-light-secondary text-secondary ms-1"
                            >
                                {employee.emergency_contacts?.length || 0}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {activeTab === 'salary'
                                ? 'active'
                                : ''}"
                            onclick={() => (activeTab = "salary")}
                        >
                            <i class="bi bi-cash-stack"></i> Historial Salarial
                            <span
                                class="badge bg-light-secondary text-secondary ms-1"
                            >
                                {employee.salary_histories?.length || 0}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {activeTab === 'projects'
                                ? 'active'
                                : ''}"
                            onclick={() => (activeTab = "projects")}
                        >
                            <i class="bi bi-diagram-3"></i> Proyectos
                            <span
                                class="badge bg-light-secondary text-secondary ms-1"
                            >
                                {employee.project_assignments?.length || 0}
                            </span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB: General -->
                    {#if activeTab === "general"}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="bi bi-person-exclamation"></i>
                                    Información Personal
                                </h5>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Nombre Completo</small
                                    >
                                    <span class="fw-semibold"
                                        >{employee.first_name}
                                        {employee.last_name}</span
                                    >
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Email</small
                                    >
                                    <span>{employee.email}</span>
                                </div>

                                {#if employee.phone}
                                    <div class="mb-3">
                                        <small class="text-muted d-block"
                                            >Teléfono</small
                                        >
                                        <span>{employee.phone}</span>
                                    </div>
                                {/if}

                                {#if employee.birth_date}
                                    <div class="mb-3">
                                        <small class="text-muted d-block"
                                            >Fecha de Nacimiento</small
                                        >
                                        <span>
                                            {new Date(
                                                employee.birth_date,
                                            ).toLocaleDateString("es-CO", {
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                            })}
                                        </span>
                                    </div>
                                {/if}

                                {#if employee.address}
                                    <div class="mb-3">
                                        <small class="text-muted d-block"
                                            >Dirección</small
                                        >
                                        <span>{employee.address}</span>
                                    </div>
                                {/if}
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="bi bi-person-lines-fill"></i>
                                    Información Laboral
                                </h5>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Departamento</small
                                    >
                                    {#if employee.department}
                                        <span class="fw-semibold"
                                            >{employee.department.name}</span
                                        >
                                    {:else}
                                        <span class="text-muted">N/A</span>
                                    {/if}
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Puesto</small
                                    >
                                    {#if employee.position}
                                        <span class="fw-semibold"
                                            >{employee.position.title}</span
                                        >
                                        <br />
                                        <small class="text-muted"
                                            >Nivel: {employee.position
                                                .level}</small
                                        >
                                    {:else}
                                        <span class="text-muted">N/A</span>
                                    {/if}
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Fecha de Contratación</small
                                    >
                                    <span>
                                        {new Date(
                                            employee.hire_date,
                                        ).toLocaleDateString("es-CO", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })}
                                    </span>
                                    <br />
                                    <small class="text-muted"
                                        >{stats.months_in_company} meses en la empresa</small
                                    >
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Salario Actual</small
                                    >
                                    <span class="fw-semibold fs-5 text-success">
                                        ${parseFloat(
                                            employee.salary,
                                        ).toLocaleString("es-CO")}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block"
                                        >Estado</small
                                    >
                                    <span
                                        class="badge {getStatusBadge(
                                            employee.status,
                                        )}"
                                    >
                                        {getStatusLabel(employee.status)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    {/if}

                    <!-- TAB: Documentos -->
                    {#if activeTab === "documents"}
                        <DocumentsTab {employee} />
                    {/if}

                    <!-- TAB: Contactos de Emergencia -->
                    {#if activeTab === "contacts"}
                        <EmergencyContactsTab {employee} />
                    {/if}

                    <!-- TAB: Historial Salarial -->
                    {#if activeTab === "salary"}
                        <SalaryHistoryTab {employee} />
                    {/if}

                    <!-- TAB: Proyectos -->
                    {#if activeTab === "projects"}
                        <ProjectsTab {employee} />
                    {/if}
                </div>
            </div>
        </div>
    </div>
</AdminLayout>
