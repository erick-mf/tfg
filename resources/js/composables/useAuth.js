import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const ROLES = Object.freeze({
    ADMIN: 'admin',
    USER: 'user',
});

const useAuthState = () => {
    const page = usePage();
    return computed(() => page.props.auth.user || null);
};

export const useAuth = () => {
    const user = useAuthState();

    const hasRole = (role) => {
        if (!user.value) return false;
        return user.value.role === role;
    };

    return {
        user,
        hasRole,
        ROLES,
    };
};
