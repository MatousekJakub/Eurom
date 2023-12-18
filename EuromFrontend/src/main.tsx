import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import 'bootstrap/dist/css/bootstrap.min.css';
import { QueryClientProvider, QueryClient } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { Container } from 'react-bootstrap';

const client = new QueryClient();
const installPrompt = () => {
	if (
		promptEvent &&
		'prompt' in promptEvent &&
		typeof promptEvent.prompt === 'function'
	)
		promptEvent.prompt();
	window.removeEventListener('click', installPrompt);
};
let promptEvent: Event | undefined = undefined;
let prompted = false;
window.addEventListener('beforeinstallprompt', (e) => {
	if (prompted) return;
	prompted = true;
	promptEvent = e;
	window.addEventListener('click', installPrompt);
});

ReactDOM.createRoot(document.getElementById('root') as HTMLElement).render(
	<React.StrictMode>
		<QueryClientProvider client={client}>
			<BrowserRouter>
				<ToastContainer />
				<Container fluid>
					<App />
				</Container>
			</BrowserRouter>
			<ReactQueryDevtools />
		</QueryClientProvider>
	</React.StrictMode>
);
