import jwtDecode from 'jwt-decode';

export type JWT = {
	iss: string;
	aud: string;
	iat: number;
	nbf: number;
	exp: number;
	data: { login: string; id: number; adminLevel: number };
};

export function useTokenData() {
	const token = localStorage.getItem('token')
		? jwtDecode<JWT>(localStorage.getItem('token') || '')
		: null;
	const clearToken = () => localStorage.removeItem('token');
	const setToken = (token: string) => localStorage.setItem('token', token);
	return { token, clearToken, setToken };
}
