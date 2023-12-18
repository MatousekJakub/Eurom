import React from 'react';
import { Modal as ModalBS } from 'react-bootstrap';

export default function Modal(props: React.ComponentProps<typeof ModalBS>) {
	return <ModalBS {...props}>{props.children}</ModalBS>;
}

Modal.Header = function Header(
	props: React.ComponentProps<typeof ModalBS.Header> & { title?: string }
) {
	const { title, ...propsWithoutTitle } = props;
	return (
		<ModalBS.Header {...propsWithoutTitle}>
			<ModalBS.Title>
				{(title && title) || propsWithoutTitle.children}
			</ModalBS.Title>
			{title && propsWithoutTitle.children}
		</ModalBS.Header>
	);
};
Modal.Body = function Body(props: React.ComponentProps<typeof ModalBS.Body>) {
	return <ModalBS.Body {...props}>{props.children}</ModalBS.Body>;
};
Modal.Footer = function Footer(
	props: React.ComponentProps<typeof ModalBS.Footer>
) {
	return <ModalBS.Footer {...props}>{props.children}</ModalBS.Footer>;
};
