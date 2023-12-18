import React from 'react';
import {
	Button,
	Modal,
	ModalBody,
	ModalFooter,
	ModalHeader,
	ModalTitle,
} from 'react-bootstrap';

export interface IPrompt {
	text: string | undefined;
	callback: (data: any) => void | undefined;
	isShown: boolean;
	handleClose: () => void;
	data: any;
}

export default function Prompt({
	text,
	callback,
	isShown,
	handleClose,
	data,
}: IPrompt) {
	return (
		<>
			<Modal show={isShown} onHide={handleClose}>
				<ModalHeader closeButton>
					<ModalTitle>Varování</ModalTitle>
				</ModalHeader>
				<ModalBody>{text}</ModalBody>
				<ModalFooter>
					<Button variant='secondary' onClick={handleClose}>
						Zavřít
					</Button>
					<Button
						variant='primary'
						onClick={() => {
							callback(data);
							handleClose();
						}}
					>
						Potvrdit
					</Button>
				</ModalFooter>
			</Modal>
		</>
	);
}
