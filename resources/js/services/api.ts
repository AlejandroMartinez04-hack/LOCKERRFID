const tokenKey = 'silocker_token';
const mockEnabled = import.meta.env.VITE_USE_MOCK_DATA !== 'false';

export const auth = {
    token: () => localStorage.getItem(tokenKey),
    save: (token: string) => localStorage.setItem(tokenKey, token),
    clear: () => localStorage.removeItem(tokenKey),
};

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
    const headers = new Headers(options.headers);
    headers.set('Accept', 'application/json');
    headers.set('Content-Type', 'application/json');

    const token = auth.token();
    if (token) {
        headers.set('Authorization', `Bearer ${token}`);
    }

    const response = await fetch(`/api/${path}`, {
        ...options,
        headers,
    });

    if (!response.ok) {
        let body: { message?: string; errors?: Record<string, string[]> } = {};

        try {
            body = await response.json();
        } catch {
            body = {};
        }

        const validationMessage = body.errors
            ? Object.values(body.errors).flat().join(' ')
            : '';
        throw new Error(
            validationMessage || body.message || `Error ${response.status}`,
        );
    }

    if (response.status === 204) {
        return undefined as T;
    }

    return response.json();
}

export async function login(email: string, password: string) {
    const result = await request<{ token: string; user: unknown }>('login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
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

export async function getResourceCollection<T>(path: string): Promise<T[]> {
    const result = await getResource<T[] | { data: T[] }>(path);

    if (Array.isArray(result)) {
        return result;
    }

    if (result && Array.isArray(result.data)) {
        return result.data;
    }

    throw new Error(
        `La respuesta de ${path} no contiene una colección válida.`,
    );
}

export async function createResource<T>(
    path: string,
    data: unknown,
): Promise<T> {
    return request<T>(path, {
        method: 'POST',
        body: JSON.stringify(data),
    });
}

export async function updateResource<T>(
    path: string,
    data: unknown,
): Promise<T> {
    return request<T>(path, {
        method: 'PATCH',
        body: JSON.stringify(data),
    });
}

export async function deleteResource(path: string): Promise<void> {
    await request<void>(path, { method: 'DELETE' });
}

export { mockEnabled };
