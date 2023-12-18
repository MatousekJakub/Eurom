import React, { useEffect, useRef, useState } from 'react';
import Button from 'react-bootstrap/esm/Button';
import Col from 'react-bootstrap/esm/Col';
import Container from 'react-bootstrap/esm/Container';
import Row from 'react-bootstrap/esm/Row';
import { Card, Spinner } from 'react-bootstrap';
import { useLocation, useNavigate } from 'react-router-dom';
import { useTokenData } from '@/lib/useTokenData';
import { useFetch } from '@/lib/useFetch';
import Form from '@/components/forms/Form';

export default function Login() {
	const email = useRef<HTMLInputElement>(null);
	const pass = useRef<HTMLInputElement>(null);
	const [isLoading, setIsLoading] = useState(false);
	const [successState, setSuccessState] = useState(0);
	const navigate = useNavigate();
	const location = useLocation();
	const [redirectPath, setRedirectPath] = useState('');
	const { setToken, clearToken } = useTokenData();
	useEffect(() => {
		clearToken();
		setRedirectPath(
			!location.state || location.state === '/login' ? '/' : location.state
		);
		return () => setRedirectPath('/');
	}, []);

	useEffect(() => {
		setSuccessState(0);
	}, [email, pass]);

	const handleClick = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
		e.preventDefault();
		setIsLoading(true);
		useFetch('/login', 'POST', {
			login: email.current?.value || '',
			pass: pass.current?.value || '',
		})
			.then((r) => {
				setIsLoading(false);
				setSuccessState(1);
				setToken(r.token);
				navigate(redirectPath);
			})
			.catch(() => {
				setIsLoading(false);
				setSuccessState(2);
			});
	};
	const variant =
		successState === 0 ? 'primary' : successState === 1 ? 'success' : 'danger';
	return (
		<Container>
			<Row className='justify-content-md-center'>
				<Col lg={6}>
					<Card style={{ padding: 20 }}>
						<Form>
							<Form.Input
								className='mb-3'
								id='email'
								label='Emailová adresa'
								type='login'
								placeholder='Zadejte email'
								required
								inputRef={email}
							/>
							<Form.Input
								className='mb-3'
								id='pass'
								type='password'
								label='Heslo'
								placeholder='Zadejte heslo'
								required
								inputRef={pass}
							/>
							<Col className='text-center'>
								<Button
									variant={variant}
									type='submit'
									onClick={(e) => handleClick(e)}
									size='lg'
								>
									{isLoading ? (
										<Spinner animation='border' size='sm' />
									) : (
										'Odeslat'
									)}
								</Button>
							</Col>
						</Form>
					</Card>
				</Col>
			</Row>
		</Container>
	);
}
