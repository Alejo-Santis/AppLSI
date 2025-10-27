<script>
    // resources/js/Components/Can.svelte
    import { page } from "@inertiajs/svelte";

    /**
     * Props del componente
     */
    let {
        permission = null,
        permissions = [],
        role = null,
        roles = [],
        requireAll = false, // true = AND, false = OR
        children,
    } = $props();

    /**
     * Verifica si el usuario cumple con los requisitos de permisos/roles
     */
    let canAccess = $derived(() => {
        const user = $page.props.auth.user;
        if (!user) return false;

        const userPermissions = user.permissions || [];
        const userRoles = user.roles || [];

        // Verificar permisos individuales
        if (permission) {
            if (!userPermissions.includes(permission)) return false;
        }

        // Verificar array de permisos
        if (permissions.length > 0) {
            if (requireAll) {
                // Requiere TODOS los permisos
                if (!permissions.every((p) => userPermissions.includes(p))) {
                    return false;
                }
            } else {
                // Requiere AL MENOS UNO
                if (!permissions.some((p) => userPermissions.includes(p))) {
                    return false;
                }
            }
        }

        // Verificar rol individual
        if (role) {
            if (!userRoles.includes(role)) return false;
        }

        // Verificar array de roles
        if (roles.length > 0) {
            if (requireAll) {
                // Requiere TODOS los roles
                if (!roles.every((r) => userRoles.includes(r))) {
                    return false;
                }
            } else {
                // Requiere AL MENOS UNO
                if (!roles.some((r) => userRoles.includes(r))) {
                    return false;
                }
            }
        }

        return true;
    });
</script>

{#if canAccess}
    {@render children()}
{/if}