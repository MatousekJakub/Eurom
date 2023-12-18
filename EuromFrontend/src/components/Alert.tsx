import React, { useState } from 'react';
import { Offcanvas } from 'react-bootstrap';

export default function Alert({
	title,
	text,
}: {
	title: string;
	text: string;
}) {
	const [show, setShow] = useState(true);

	const handleClose = () => setShow(false);
	return (
		<Offcanvas show={show} onHide={handleClose} placement={'bottom'}>
			<Offcanvas.Header closeButton>
				<Offcanvas.Title>{title}</Offcanvas.Title>
			</Offcanvas.Header>
			<Offcanvas.Body>{text}</Offcanvas.Body>
		</Offcanvas>
	);
}
