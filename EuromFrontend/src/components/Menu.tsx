import React from 'react';
import Nav from 'react-bootstrap/Nav';
import { LinkContainer } from 'react-router-bootstrap';
import * as Icons from 'react-bootstrap-icons';
import {
	Container,
	Navbar,
	Offcanvas,
	NavDropdown,
	Row,
	Col,
} from 'react-bootstrap';

export default function Menu() {
	const expand = 'lg';
	return (
		<Container>
			<Row>
				<Col>
					<Navbar bg='light' expand={expand} className='mb-3'>
						<LinkContainer to='/'>
							<Navbar.Brand href='/'>
								<Icons.House /> Domů
							</Navbar.Brand>
						</LinkContainer>
						<Navbar.Toggle aria-controls={`offcanvasNavbar-expand-${expand}`} />
						<Navbar.Offcanvas
							id={`offcanvasNavbar-expand-${expand}`}
							aria-labelledby={`offcanvasNavbarLabel-expand-${expand}`}
							placement='end'
						>
							<Offcanvas.Header closeButton>
								<Offcanvas.Title id={`offcanvasNavbarLabel-expand-${expand}`}>
									Menu
								</Offcanvas.Title>
							</Offcanvas.Header>
							<Offcanvas.Body>
								<Nav className='justify-content-end flex-grow-1 pe-3'>
									<NavDropdown
										title={
											<>
												<Icons.Tools /> Dropdown
											</>
										}
									>
										<LinkContainer to='/link'>
											<NavDropdown.Item href='/link'>
												<Icons.Calculator /> Odkaz
											</NavDropdown.Item>
										</LinkContainer>
										<LinkContainer to='/link'>
											<NavDropdown.Item href='/link'>
												<Icons.ListCheck /> Odkaz
											</NavDropdown.Item>
										</LinkContainer>
									</NavDropdown>
									<Nav.Link href='/login'>Odhlásit</Nav.Link>
								</Nav>
							</Offcanvas.Body>
						</Navbar.Offcanvas>
					</Navbar>
				</Col>
			</Row>
		</Container>
	);
}
