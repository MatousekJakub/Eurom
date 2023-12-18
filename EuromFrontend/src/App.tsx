import React from 'react';
import jwtDecode from 'jwt-decode';
import { useNavigate, useLocation } from 'react-router-dom';
import { useEffect } from 'react';
import { Bearer } from './interfaces/Bearer';

import Router from './Router';

export default function App() {
	const location = useLocation();
	const navigate = useNavigate();
	const handleNotLogged = () =>
		navigate('/login', { state: location.pathname });
	useEffect(() => {
		let logged = true;
		let bearer: Bearer | undefined = undefined;
		const jwt = localStorage.getItem('token');
		if (jwt === null) return handleNotLogged();
		try {
			bearer = jwtDecode(jwt);
		} catch (error) {
			logged = false;
		}
		if (
			(!bearer || !logged || Date.now() / 1000 > bearer.exp) &&
			location.pathname != '/login'
		) {
			return handleNotLogged();
		}
	}, []);
	return <Router />;
}
