import toast from './toast';
import { useConfig } from './useConfig';

export function useFetch(
	url: string,
	method: 'GET' | 'POST' | 'PUT' | 'DELETE' = 'GET',
	body?: any,
) {
	const { baseApiPath } = useConfig();
	const f = fetch(`${baseApiPath}${url}`, {
		method: method,
		headers: {
			'Content-Type': 'application/json',
			Authorization: `Bearer ${localStorage.getItem('token')}`,
		},
		body: JSON.stringify(body),
	})
		.then((res: any) => {
			if (res.status === 204) {
				toast('Úspěšně splněno');
				return;
			}
			if (res.status === 400) {
				toast('400 Nelze provést', { type: 'error' });
			} else if (res.status === 401)
				toast('401 Vyžadováno přihlášení', { type: 'error' });
			else if (res.status === 403)
				toast('403 Nedostatečné oprávnění', { type: 'error' });
			else if (res.status === 404) toast('404 Neexistuje', { type: 'error' });
			else if (res.status === 500)
				toast('500 Chyba serveru', { type: 'error' });
			else if (res.status > 400 && res.status < 500)
				toast(`${res.status} Nespecifikovaná chyba`, { type: 'error' });
			let outJson;
			try {
				outJson = res.json();
			} catch (e) {
				console.log('caught');
				outJson = res.text();
			}
			return outJson || '';
		})
		.catch((e) => {
			console.log(e);
		});
	return f;
}
