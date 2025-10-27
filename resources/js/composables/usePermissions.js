// resources/js/composables/usePermissions.js

import { page } from '@inertiajs/svelte';
import { derived } from 'svelte/store';

/**
 * Store derivado que contiene la información del usuario autenticado
 */
export const authUser = derived(page, ($page) => $page.props.auth.user);

/**
 * Store derivado que contiene los permisos del usuario
 */
export const userPermissions = derived(page, ($page) => 
    $page.props.auth.user?.permissions || []
);

/**
 * Store derivado que contiene los roles del usuario
 */
export const userRoles = derived(page, ($page) => 
    $page.props.auth.user?.roles || []
);

/**
 * Verifica si el usuario tiene un permiso específico
 * @param {string} permission - Nombre del permiso
 * @returns {boolean}
 */
export function hasPermission(permission) {
    const permissions = page.props?.auth?.user?.permissions || [];
    return permissions.includes(permission);
}

/**
 * Verifica si el usuario tiene TODOS los permisos especificados
 * @param {string[]} permissions - Array de nombres de permisos
 * @returns {boolean}
 */
export function hasAllPermissions(permissions) {
    const userPerms = page.props?.auth?.user?.permissions || [];
    return permissions.every(permission => userPerms.includes(permission));
}

/**
 * Verifica si el usuario tiene AL MENOS UNO de los permisos especificados
 * @param {string[]} permissions - Array de nombres de permisos
 * @returns {boolean}
 */
export function hasAnyPermission(permissions) {
    const userPerms = page.props?.auth?.user?.permissions || [];
    return permissions.some(permission => userPerms.includes(permission));
}

/**
 * Verifica si el usuario tiene un rol específico
 * @param {string} role - Nombre del rol
 * @returns {boolean}
 */
export function hasRole(role) {
    const roles = page.props?.auth?.user?.roles || [];
    return roles.includes(role);
}

/**
 * Verifica si el usuario tiene TODOS los roles especificados
 * @param {string[]} roles - Array de nombres de roles
 * @returns {boolean}
 */
export function hasAllRoles(roles) {
    const userRoles = page.props?.auth?.user?.roles || [];
    return roles.every(role => userRoles.includes(role));
}

/**
 * Verifica si el usuario tiene AL MENOS UNO de los roles especificados
 * @param {string[]} roles - Array de nombres de roles
 * @returns {boolean}
 */
export function hasAnyRole(roles) {
    const userRoles = page.props?.auth?.user?.roles || [];
    return roles.some(role => userRoles.includes(role));
}

/**
 * Verifica si el usuario es Admin
 * @returns {boolean}
 */
export function isAdmin() {
    return page.props?.auth?.user?.is_admin || false;
}

/**
 * Verifica si el usuario es HR
 * @returns {boolean}
 */
export function isHR() {
    return page.props?.auth?.user?.is_hr || false;
}

/**
 * Verifica si el usuario es Manager
 * @returns {boolean}
 */
export function isManager() {
    return page.props?.auth?.user?.is_manager || false;
}

/**
 * Verifica si el usuario es Employee
 * @returns {boolean}
 */
export function isEmployee() {
    return page.props?.auth?.user?.is_employee || false;
}

/**
 * Obtiene el rol básico del usuario (admin, hr, manager, employee)
 * @returns {string|null}
 */
export function getBasicRole() {
    return page.props?.auth?.user?.role || null;
}