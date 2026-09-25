const tokenKey = 'silocker_token';
const mockEnabled = import.meta.env.VITE_USE_MOCK_DATA !== 'false';

export const auth = {
    token: () => localStorage.getItem(tokenKey),
    save: (token: string) => localStorage.setItem(tokenKey, token),
    clear: () => localStorage.removeItem(tokenKey),
};

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
    const response = await fetch(`/api/${path}`, {
        ...options,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(auth.token() ? { Authorization: `Bearer ${auth.token()}` } : {}),
            ...(options.headers || {}),
        },
    });

    if (!response.ok) {
        const body = await response.json().catch(() => ({}));
        throw new Error(body.message || `Error ${response.status}`);
    }
    return response.json();
}

export async function login(email: string, password: string) {
    const result = await request<{ token: string; user: unknown }>('login', {
        method: 'POST', body: JSON.stringify({ email, password }),
    });
    auth.save(result.token);
    return result.user;
}

export async function me() {
    const result = await request<{ user: unknown }>('me');
    return result.user;
}

export async function logout() {
    if (auth.token()) await request('logout', { method: 'POST' });
    auth.clear();
}

export async function getResource<T>(path: string): Promise<T> {
    const result = await request<T>(path);
    return result;
}

export { mockEnabled };
