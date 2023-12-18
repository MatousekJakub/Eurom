export interface Bearer {
	aud: string;
	data: { login: string; id: number; adminLevel: number };
	exp: number;
	iat: number;
	iss: string;
	nbf: number;
}
