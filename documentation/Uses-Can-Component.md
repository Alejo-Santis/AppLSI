# 📚 Ejemplos de Uso - Permisos y Roles en Frontend

## 🎯 Datos Disponibles en Todos los Componentes

Después de configurar `HandleInertiaRequests`, tendrás acceso a:

```javascript
$page.props.auth.user = {
    id: 1,
    name: "Juan Pérez",
    email: "juan@example.com",
    role: "admin", // Rol básico (enum)
    is_active: true,
    last_login: "2024-01-15 10:30:00",
    
    // Roles de Spatie
    roles: ["Admin", "Manager"],
    
    // Permisos efectivos
    permissions: [
        "dashboard.view",
        "departments.view",
        "departments.create",
        // ... todos los permisos
    ],
    
    // Helpers booleanos
    is_admin: true,
    is_hr: false,
    is_manager: true,
    is_employee: false,
}
```

---

## 🔧 Método 1: Usando el Componente `<Can>`

### Ejemplo 1: Verificar un permiso simple

```svelte
<script>
    import Can from "@/Components/Can.svelte";
</script>

<Can permission="departments.create">
    <button class="btn btn-primary">
        <i class="bi bi-plus"></i>
        Crear Departamento
    </button>
</Can>
```

### Ejemplo 2: Verificar múltiples permisos (OR)

```javascript
<!-- Muestra el botón si tiene AL MENOS UNO de estos permisos -->
<Can permissions={["departments.create", "departments.edit"]}>
    <button class="btn btn-primary">Gestionar Departamentos</button>
</Can>
```

### Ejemplo 3: Verificar múltiples permisos (AND)

```javascript
<!-- Muestra el botón SOLO si tiene TODOS los permisos -->
<Can 
    permissions={["departments.create", "departments.delete"]} 
    requireAll={true}
>
    <button class="btn btn-danger">Acciones Administrativas</button>
</Can>
```

### Ejemplo 4: Verificar por rol

```javascript
<!-- Solo para Admin -->
<Can role="Admin">
    <Link href="/admin/roles">
        <i class="bi bi-shield-lock"></i>
        Gestión de Roles
    </Link>
</Can>
```

### Ejemplo 5: Verificar múltiples roles

```javascript
<!-- Para Admin o HR -->
<Can roles={["Admin", "HR"]}>
    <Link href="/employees">
        <i class="bi bi-people"></i>
        Empleados
    </Link>
</Can>
```

### Ejemplo 6: Combinar roles y permisos

```javascript
<!-- Debe ser Admin Y tener el permiso específico -->
<Can role="Admin" permission="users.manage-permissions" requireAll={true}>
    <button class="btn btn-warning">Gestión Avanzada</button>
</Can>
```

---

## 🛠️ Método 2: Usando Helpers de JavaScript

### Importar los helpers

```javascript
<script>
    import { 
        hasPermission, 
        hasRole, 
        isAdmin,
        hasAnyPermission,
        hasAllPermissions 
    } from "@/composables/usePermissions.js";
</script>
```

### Ejemplo 1: Condicionales simples

```javascript
<script>
    import { hasPermission, isAdmin } from "@/composables/usePermissions.js";
</script>

{#if hasPermission('departments.create')}
    <button class="btn btn-primary">Crear Departamento</button>
{/if}

{#if isAdmin()}
    <div class="admin-panel">
        <!-- Panel de administración -->
    </div>
{/if}
```

### Ejemplo 2: Múltiples condiciones

```javascript
<script>
    import { hasAnyPermission, hasRole } from "@/composables/usePermissions.js";
</script>

{#if hasAnyPermission(['employees.edit', 'employees.delete'])}
    <div class="btn-group">
        {#if hasPermission('employees.edit')}
            <button class="btn btn-warning">Editar</button>
        {/if}
        {#if hasPermission('employees.delete')}
            <button class="btn btn-danger">Eliminar</button>
        {/if}
    </div>
{/if}

{#if hasRole('Admin') || hasRole('HR')}
    <Link href="/admin/users">Gestión de Usuarios</Link>
{/if}
```

### Ejemplo 3: En funciones

```javascript
<script>
    import { hasPermission } from "@/composables/usePermissions.js";

    function handleAction() {
        if (!hasPermission('departments.delete')) {
            alert('No tienes permiso para eliminar departamentos');
            return;
        }
        
        // Proceder con la acción
        deleteDepartment();
    }
</script>

<button onclick={handleAction}>Eliminar</button>
```

---

## 📋 Método 3: Acceso Directo con `$page`

### Ejemplo 1: Verificación directa

```javascript
<script>
    import { page } from "@inertiajs/svelte";
</script>

{#if $page.props.auth.user.permissions.includes('departments.create')}
    <button>Crear</button>
{/if}

{#if $page.props.auth.user.is_admin}
    <div class="admin-menu">
        <!-- Menú de administración -->
    </div>
{/if}
```

### Ejemplo 2: Mostrar nombre del usuario

```javascript
<script>
    import { page } from "@inertiajs/svelte";
</script>

<div class="user-info">
    <h5>Bienvenido, {$page.props.auth.user.name}</h5>
    <p class="text-muted">{$page.props.auth.user.email}</p>
    <span class="badge bg-primary">
        {$page.props.auth.user.role.toUpperCase()}
    </span>
</div>
```

---

## 🎨 Ejemplos Completos de Componentes

### Ejemplo 1: Menú Lateral con Permisos

```javascript
<script>
    import { Link } from "@inertiajs/svelte";
    import Can from "@/Components/Can.svelte";
    import { page } from "@inertiajs/svelte";
</script>

<aside class="sidebar">
    <div class="user-info">
        <h5>{$page.props.auth.user.name}</h5>
        <span class="badge">{$page.props.auth.user.role}</span>
    </div>

    <ul class="nav flex-column">
        <!-- Dashboard - Todos pueden ver -->
        <li class="nav-item">
            <Link href="/dashboard" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </Link>
        </li>

        <!-- Departamentos - Solo con permiso -->
        <Can permission="departments.view">
            <li class="nav-item">
                <Link href="/departments" class="nav-link">
                    <i class="bi bi-building"></i>
                    Departamentos
                </Link>
            </li>
        </Can>

        <!-- Empleados - Admin o HR -->
        <Can roles={["Admin", "HR"]}>
            <li class="nav-item">
                <Link href="/employees" class="nav-link">
                    <i class="bi bi-people"></i>
                    Empleados
                </Link>
            </li>
        </Can>

        <!-- Proyectos - Con cualquiera de estos permisos -->
        <Can permissions={["projects.view-all", "projects.view-assigned"]}>
            <li class="nav-item">
                <Link href="/projects" class="nav-link">
                    <i class="bi bi-kanban"></i>
                    Proyectos
                </Link>
            </li>
        </Can>

        <!-- Administración - Solo Admin -->
        <Can role="Admin">
            <li class="nav-item">
                <div class="nav-link collapsed" data-bs-toggle="collapse" href="#admin-menu">
                    <i class="bi bi-shield-lock"></i>
                    <span>Administración</span>
                </div>
                <div id="admin-menu" class="collapse">
                    <ul class="nav flex-column ms-3">
                        <li><Link href="/admin/roles">Roles</Link></li>
                        <li><Link href="/admin/users">Usuarios</Link></li>
                    </ul>
                </div>
            </li>
        </Can>
    </ul>
</aside>
```

### Ejemplo 2: Tabla con Acciones según Permisos

```javascript
<script>
    import { Link } from "@inertiajs/svelte";
    import { hasPermission } from "@/composables/usePermissions.js";

    let { departments } = $props();
</script>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        {#each departments as dept}
            <tr>
                <td>{dept.name}</td>
                <td>{dept.code}</td>
                <td>
                    <div class="btn-group">
                        <!-- Ver - Todos pueden -->
                        {#if hasPermission('departments.show')}
                            <Link 
                                href={`/departments/show/${dept.id}`}
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-eye"></i>
                            </Link>
                        {/if}

                        <!-- Editar - Solo con permiso -->
                        {#if hasPermission('departments.edit')}
                            <Link 
                                href={`/departments/edit/${dept.id}`}
                                class="btn btn-sm btn-outline-warning"
                            >
                                <i class="bi bi-pencil"></i>
                            </Link>
                        {/if}

                        <!-- Eliminar - Solo Admin o HR -->
                        {#if hasPermission('departments.delete')}
                            <button 
                                class="btn btn-sm btn-outline-danger"
                                onclick={() => confirmDelete(dept)}
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        {/if}
                    </div>
                </td>
            </tr>
        {/each}
    </tbody>
</table>
```

### Ejemplo 3: Dashboard Personalizado por Rol

```javascript
<script>
    import { page } from "@inertiajs/svelte";
    import { isAdmin, isHR, isManager } from "@/composables/usePermissions.js";
</script>

<div class="dashboard">
    <h1>Bienvenido, {$page.props.auth.user.name}</h1>

    <div class="row">
        {#if isAdmin()}
            <!-- Dashboard para Admin -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Usuarios</h5>
                        <h2>150</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Roles Activos</h5>
                        <h2>8</h2>
                    </div>
                </div>
            </div>
        {/if}

        {#if isHR() || isAdmin()}
            <!-- Dashboard para HR -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Empleados</h5>
                        <h2>250</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Departamentos</h5>
                        <h2>12</h2>
                    </div>
                </div>
            </div>
        {/if}

        {#if isManager()}
            <!-- Dashboard para Manager -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Mi Equipo</h5>
                        <h2>15</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Proyectos Activos</h5>
                        <h2>3</h2>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>
```

---

## 🎯 Mejores Prácticas

### ✅ DO (Hacer)

```javascript
<!-- Usar el componente Can para claridad -->
<Can permission="departments.create">
    <button>Crear</button>
</Can>

<!-- Usar helpers para lógica -->
<script>
    import { hasPermission } from "@/composables/usePermissions.js";
    
    function handleAction() {
        if (!hasPermission('action.perform')) return;
        // ...
    }
</script>

<!-- Combinar múltiples verificaciones -->
<Can permissions={["edit", "delete"]} requireAll={true}>
    <div>Acciones Avanzadas</div>
</Can>
```

### ❌ DON'T (Evitar)

```javascript
<!-- Evitar lógica compleja en templates -->
{#if $page.props.auth.user && $page.props.auth.user.permissions && $page.props.auth.user.permissions.includes('some.permission')}
    <!-- Demasiado verbose -->
{/if}

<!-- No hacer verificaciones redundantes -->
{#if isAdmin() && hasRole('Admin')}
    <!-- isAdmin() ya verifica hasRole('Admin') -->
{/if}
```

---

## 📝 Resumen de APIs Disponibles

| Helper | Descripción | Ejemplo |
|--------|-------------|---------|
| `hasPermission(permission)` | Verifica un permiso | `hasPermission('departments.create')` |
| `hasAllPermissions([])` | Verifica todos los permisos | `hasAllPermissions(['edit', 'delete'])` |
| `hasAnyPermission([])` | Verifica al menos uno | `hasAnyPermission(['view', 'edit'])` |
| `hasRole(role)` | Verifica un rol | `hasRole('Admin')` |
| `hasAnyRole([])` | Verifica al menos un rol | `hasAnyRole(['Admin', 'HR'])` |
| `isAdmin()` | Es admin? | `isAdmin()` |
| `isHR()` | Es HR? | `isHR()` |
| `isManager()` | Es manager? | `isManager()` |
| `isEmployee()` | Es employee? | `isEmployee()` |

¡Tu sistema ahora tiene control de acceso completo en el frontend! 🎉
